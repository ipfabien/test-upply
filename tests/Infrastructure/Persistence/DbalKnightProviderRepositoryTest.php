<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Persistence;

use App\Domain\Knight;
use App\Domain\KnightSet;
use App\Infrastructure\Persistence\DbalKnightProviderRepository;
use App\Shared\Exception\NotFoundException;
use App\Tests\Support\Database\DatabaseIsolationByClassTrait;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

final class DbalKnightProviderRepositoryTest extends KernelTestCase
{
    use DatabaseIsolationByClassTrait;

    private Connection $connection;
    private DbalKnightProviderRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->connection = $container->get(Connection::class);
        $this->repository = $container->get(DbalKnightProviderRepository::class);
    }

    public function testFindNotFoundThrows(): void
    {
        $this->expectException(NotFoundException::class);
        $this->repository->find('00000000-0000-0000-0000-000000000000');
    }

    public function testFindReturnsKnight(): void
    {
        $externalId = Uuid::v4()->toRfc4122();
        $this->connection->insert('knight', [
            'external_id' => $externalId,
            'name' => 'Bors',
            'strength' => 12,
            'weapon_power' => 34,
            'created_at' => (new \DateTimeImmutable('2024-01-01T00:00:00+00:00'))->format('Y-m-d H:i:sP'),
        ]);

        $knight = $this->repository->find($externalId);
        Assert::assertInstanceOf(Knight::class, $knight);
        Assert::assertSame($externalId, $knight->getId());
        Assert::assertSame('Bors', $knight->name);
        Assert::assertSame(12, $knight->strength);
        Assert::assertSame(34, $knight->weaponPower);
    }

    public function testGetAllReturnsOrderedAscAndLimitedTo50(): void
    {
        $base = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        // Insert 60 rows with incremental created_at
        for ($i = 1; $i <= 60; $i++) {
            $this->connection->insert('knight', [
                'external_id' => Uuid::v4()->toRfc4122(),
                'name' => \sprintf('K%02d', $i),
                'strength' => $i,
                'weapon_power' => $i,
                'created_at' => $base->modify(\sprintf('+%d seconds', $i))->format('Y-m-d H:i:sP'),
            ]);
        }

        $set = $this->repository->getAll();
        Assert::assertInstanceOf(KnightSet::class, $set);
        Assert::assertCount(50, $set);

        // Build arrays to check order by created_at asc → first inserted rows (smallest i) first
        $allRows = $this->connection->fetchAllAssociative(
            'SELECT external_id FROM knight ORDER BY created_at ASC'
        );
        $expectedFirst = $allRows[0]['external_id'];
        $expectedLast = $allRows[49]['external_id']; // due to LIMIT 50

        $ids = [];
        foreach ($set as $externalId => $knight) {
            $ids[] = $externalId;
        }

        Assert::assertSame($expectedFirst, $ids[0]);
        Assert::assertSame($expectedLast, $ids[49]);
    }

    public function testGetAllKeysAreExternalIds(): void
    {
        $this->connection->executeStatement('TRUNCATE TABLE knight RESTART IDENTITY CASCADE');
        $idA = Uuid::v4()->toRfc4122();
        $idB = Uuid::v4()->toRfc4122();
        $this->connection->insert('knight', [
            'external_id' => $idA,
            'name' => 'A',
            'strength' => 1,
            'weapon_power' => 1,
            'created_at' => '2024-01-01 00:00:00+00:00',
        ]);
        $this->connection->insert('knight', [
            'external_id' => $idB,
            'name' => 'B',
            'strength' => 2,
            'weapon_power' => 2,
            'created_at' => '2024-01-01 00:00:01+00:00',
        ]);

        $set = $this->repository->getAll();
        $keys = [];
        foreach ($set as $externalId => $k) {
            $keys[] = $externalId;
        }
        Assert::assertSame([$idA, $idB], $keys);
    }
}
