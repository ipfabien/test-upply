<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\KnightRequest;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class KnightRequestTest extends TestCase
{
    public function testDenormalizeValidData(): void
    {
        $dto = KnightRequest::denormalize([
            'name' => 'Arthur',
            'strength' => 10,
            'weapon_power' => 20,
        ]);

        $this->assertSame('Arthur', $dto->name);
        $this->assertSame(10, $dto->strength);
        $this->assertSame(20, $dto->weaponPower);
    }

    public function testDenormalizeAcceptsIntegerish(): void
    {
        $dto = KnightRequest::denormalize([
            'name' => 'Bors',
            'strength' => '11',
            'weapon_power' => '22',
        ]);

        $this->assertSame(11, $dto->strength);
        $this->assertSame(22, $dto->weaponPower);
    }

    public function testDenormalizeMissingName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        KnightRequest::denormalize([
            'strength' => 10,
            'weapon_power' => 20,
        ]);
    }

    public function testDenormalizeEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        KnightRequest::denormalize([
            'name' => '',
            'strength' => 10,
            'weapon_power' => 20,
        ]);
    }

    public function testDenormalizeMissingStrength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        KnightRequest::denormalize([
            'name' => 'Guenièvre',
            'weapon_power' => 20,
        ]);
    }

    public function testDenormalizeNonIntegerStrength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        KnightRequest::denormalize([
            'name' => 'Perceval',
            'strength' => 'ten',
            'weapon_power' => 20,
        ]);
    }

    public function testDenormalizeMissingWeaponPower(): void
    {
        $this->expectException(InvalidArgumentException::class);
        KnightRequest::denormalize([
            'name' => 'Lancelot',
            'strength' => 10,
        ]);
    }

    public function testDenormalizeNonIntegerWeaponPower(): void
    {
        $this->expectException(InvalidArgumentException::class);
        KnightRequest::denormalize([
            'name' => 'Gauvain',
            'strength' => 10,
            'weapon_power' => 'twenty',
        ]);
    }

    public function testNormalize(): void
    {
        $dto = KnightRequest::denormalize([
            'name' => 'Arthur',
            'strength' => 10,
            'weapon_power' => 20,
        ]);

        $data = $dto->normalize();
        $this->assertSame([
            'name' => 'Arthur',
            'strength' => 10,
            'weapon_power' => 20,
        ], $data);
    }
}
