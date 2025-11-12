<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Knight;
use App\Domain\KnightProviderRepository;
use App\Domain\KnightSet;
use App\Shared\Exception\NotFoundException;
use App\Shared\Exception\RuntimeException;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Throwable;

class DbalKnightProviderRepository implements KnightProviderRepository
{
    public function __construct(private Connection $connection, private ?LoggerInterface $logger = null)
    {
    }

    /** @inheritDoc */
    public function find(string $id): Knight
    {
        try {
            $row = $this->connection->fetchAssociative(
                'SELECT external_id, name, strength, weapon_power FROM knight WHERE external_id = :id',
                ['id' => $id]
            );

            if (!$row) {
                $this->logger?->info('db.not_found.knight', ['external_id' => $id]);
                throw new NotFoundException(\sprintf('Knight #%s not found.', $id));
            }

            return Knight::create(
                $row['external_id'],
                $row['name'],
                (int) $row['strength'],
                (int) $row['weapon_power']
            );
        } catch (Throwable $e) {
            // Do not wrap already-known App exceptions
            if ($e instanceof NotFoundException) {
                throw $e;
            }

            $this->logger?->error('db.error.runtime', [
                'operation' => 'select_knight_by_external_id',
                'external_id' => $id,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Database error.', $e);
        }
    }

    public function getAll(): KnightSet
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT external_id, name, strength, weapon_power 
             FROM knight 
             ORDER BY created_at ASC 
             LIMIT 50'
        );

        $knights = [];

        foreach ($rows as $row) {
            $knights[] = Knight::create(
                $row['external_id'],
                $row['name'],
                (int) $row['strength'],
                (int) $row['weapon_power']
            );
        }

        return new KnightSet(...$knights);
    }
}
