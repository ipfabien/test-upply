<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\KnightRequest;
use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class KnightRequestTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    public function testDenormalizeValidData(): void
    {
        $name = $this->faker->firstName();
        $strength = $this->faker->numberBetween(0, 100);
        $weaponPower = $this->faker->numberBetween(0, 100);

        $dto = KnightRequest::denormalize([
            'name' => $name,
            'strength' => $strength,
            'weapon_power' => $weaponPower,
        ]);

        $this->assertSame($name, $dto->name);
        $this->assertSame($strength, $dto->strength);
        $this->assertSame($weaponPower, $dto->weaponPower);
    }

    public function testDenormalizeAcceptsIntegerish(): void
    {
        $name = $this->faker->firstName();
        $strength = $this->faker->numberBetween(0, 100);
        $weaponPower = $this->faker->numberBetween(0, 100);

        $dto = KnightRequest::denormalize([
            'name' => $name,
            'strength' => (string) $strength,
            'weapon_power' => (string) $weaponPower,
        ]);

        $this->assertSame($strength, $dto->strength);
        $this->assertSame($weaponPower, $dto->weaponPower);
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
            'name' => $this->faker->firstName(),
            'weapon_power' => 20,
        ]);
    }

    public function testDenormalizeNonIntegerStrength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        KnightRequest::denormalize([
            'name' => $this->faker->firstName(),
            'strength' => 'ten',
            'weapon_power' => 20,
        ]);
    }

    public function testDenormalizeMissingWeaponPower(): void
    {
        $this->expectException(InvalidArgumentException::class);
        KnightRequest::denormalize([
            'name' => $this->faker->firstName(),
            'strength' => 10,
        ]);
    }

    public function testDenormalizeNonIntegerWeaponPower(): void
    {
        $this->expectException(InvalidArgumentException::class);
        KnightRequest::denormalize([
            'name' => $this->faker->firstName(),
            'strength' => 10,
            'weapon_power' => 'twenty',
        ]);
    }

    public function testNormalize(): void
    {
        $name = $this->faker->firstName();
        $strength = $this->faker->numberBetween(0, 100);
        $weaponPower = $this->faker->numberBetween(0, 100);

        $dto = KnightRequest::denormalize([
            'name' => $name,
            'strength' => $strength,
            'weapon_power' => $weaponPower,
        ]);

        $data = $dto->normalize();
        $this->assertSame([
            'name' => $name,
            'strength' => $strength,
            'weapon_power' => $weaponPower,
        ], $data);
    }
}
