<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\CreateKnight;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CreateKnightTest extends TestCase
{
    public function testCreateValid(): void
    {
        $id = Uuid::v4()->toRfc4122();
        $vo = CreateKnight::create($id, 'Arthur', 10, 20);

        $this->assertSame($id, $vo->externalId);
        $this->assertSame('Arthur', $vo->name);
        $this->assertSame(10, $vo->strength);
        $this->assertSame(20, $vo->weaponPower);
    }

    public function testCreateInvalidUuid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CreateKnight::create('not-a-uuid', 'Arthur', 10, 20);
    }

    public function testCreateEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CreateKnight::create(Uuid::v4()->toRfc4122(), '', 10, 20);
    }

    public function testCreateNegativeStrength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CreateKnight::create(Uuid::v4()->toRfc4122(), 'Arthur', -1, 20);
    }

    public function testCreateNegativeWeaponPower(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CreateKnight::create(Uuid::v4()->toRfc4122(), 'Arthur', 10, -5);
    }
}
