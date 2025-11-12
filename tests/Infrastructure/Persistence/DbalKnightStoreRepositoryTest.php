<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Persistence;

use App\Domain\CreateKnight;
use App\Infrastructure\Persistence\DbalKnightStoreRepository;
use App\Shared\Exception\BadRequestException;
use App\Tests\Support\Database\DatabaseIsolationByClassTrait;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Uid\Uuid;

final class DbalKnightStoreRepositoryTest extends KernelTestCase
{
    use DatabaseIsolationByClassTrait;

    private Connection $connection;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->connection = self::getContainer()->get(Connection::class);
    }

    public function testSaveCreatesRowWithFrozenCreatedAt(): void
    {
        $clock = new MockClock(new \DateTimeImmutable('2024-01-01T00:00:00+00:00'));
        $repo = new DbalKnightStoreRepository($this->connection, $clock);

        $externalId = Uuid::v4()->toRfc4122();
        $vo = CreateKnight::create($externalId, 'Lancelot', 10, 20);

        $repo->save($vo);

        $row = $this->connection->fetchAssociative(
            'SELECT external_id, name, strength, weapon_power, created_at FROM knight WHERE external_id = :id',
            ['id' => $externalId]
        );

        Assert::assertNotFalse($row);
        Assert::assertSame($externalId, $row['external_id']);
        Assert::assertSame('Lancelot', $row['name']);
        Assert::assertSame(10, (int) $row['strength']);
        Assert::assertSame(20, (int) $row['weapon_power']);
        $createdAt = new \DateTimeImmutable((string) $row['created_at']);
        Assert::assertSame('2024-01-01 00:00:00+00:00', $createdAt->format('Y-m-d H:i:sP'));
    }

    public function testSaveDuplicateExternalIdThrowsBadRequest(): void
    {
        $clock = new MockClock(new \DateTimeImmutable('2024-01-01T00:00:00+00:00'));
        $repo = new DbalKnightStoreRepository($this->connection, $clock);

        $externalId = Uuid::v4()->toRfc4122();
        $vo1 = CreateKnight::create($externalId, 'Gauvain', 5, 5);
        $vo2 = CreateKnight::create($externalId, 'Perceval', 7, 7);

        $repo->save($vo1);
        $this->expectException(BadRequestException::class);
        $repo->save($vo2);
    }

    public function testMultipleSavesOrderByCreatedAt(): void
    {
        // Isolate this test to focus on ordering
        $this->connection->executeStatement('TRUNCATE TABLE knight RESTART IDENTITY CASCADE');

        $clock1 = new MockClock(new \DateTimeImmutable('2024-01-01T00:00:00+00:00'));
        $repo1 = new DbalKnightStoreRepository($this->connection, $clock1);
        $id1 = Uuid::v4()->toRfc4122();
        $repo1->save(CreateKnight::create($id1, 'Alpha', 1, 1));

        $clock2 = new MockClock(new \DateTimeImmutable('2024-01-01T00:00:05+00:00'));
        $repo2 = new DbalKnightStoreRepository($this->connection, $clock2);
        $id2 = Uuid::v4()->toRfc4122();
        $repo2->save(CreateKnight::create($id2, 'Beta', 2, 2));

        $rows = $this->connection->fetchAllAssociative(
            'SELECT external_id, created_at FROM knight ORDER BY created_at ASC'
        );
        Assert::assertCount(2, $rows);
        Assert::assertSame($id1, $rows[0]['external_id']);
        Assert::assertSame($id2, $rows[1]['external_id']);
        $first = new \DateTimeImmutable((string) $rows[0]['created_at']);
        $second = new \DateTimeImmutable((string) $rows[1]['created_at']);
        Assert::assertSame('2024-01-01 00:00:00+00:00', $first->format('Y-m-d H:i:sP'));
        Assert::assertSame('2024-01-01 00:00:05+00:00', $second->format('Y-m-d H:i:sP'));
    }
}
