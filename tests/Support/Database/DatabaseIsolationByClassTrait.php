<?php

declare(strict_types=1);

namespace App\Tests\Support\Database;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

trait DatabaseIsolationByClassTrait
{
    public static function setUpBeforeClass(): void
    {
        self::truncateDomainTables();
    }

    public static function tearDownAfterClass(): void
    {
        self::truncateDomainTables();
    }

    private static function truncateDomainTables(): void
    {
        if (!is_subclass_of(static::class, KernelTestCase::class)) {
            return;
        }

        static::bootKernel();
        $container = static::getContainer();

        /** @var Connection $connection */
        $connection = $container->get(Connection::class);
        $connection->executeStatement('TRUNCATE TABLE knight RESTART IDENTITY CASCADE');
        static::ensureKernelShutdown();
    }
}
