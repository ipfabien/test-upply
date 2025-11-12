<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\CreateKnight;
use App\Domain\KnightStoreRepository;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\RuntimeException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Clock\ClockInterface;
use Throwable;

class DbalKnightStoreRepository implements KnightStoreRepository
{
    public function __construct(
        private Connection $connection,
        private ClockInterface $clock,
        private ?LoggerInterface $logger = null
    ) {
    }
    /** @inheritDoc */
    public function save(CreateKnight $createKnight): void
    {
        try {
            $this->connection->insert('knight', [
                'external_id' => $createKnight->externalId,
                'name' => $createKnight->name,
                'strength' => $createKnight->strength,
                'weapon_power' => $createKnight->weaponPower,
                'created_at' => $this->clock->now()->format('Y-m-d H:i:sP'),
            ]);
        } catch (UniqueConstraintViolationException $e) {
            $this->logger?->warning('db.error.unique_violation', [
                'operation' => 'create knight',
                'external_id' => $createKnight->externalId,
                'error' => $e->getMessage(),
            ]);
            throw new BadRequestException('Duplicate external id.', $e);
        } catch (Throwable $e) {
            $this->logger?->error('db.error.runtime', [
                'operation' => 'create knight',
                'external_id' => $createKnight->externalId,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException('Database error.', $e);
        }
    }
}
