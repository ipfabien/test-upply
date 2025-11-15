<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Knight;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class KnightTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    public function testGetIdReturnsExternalId(): void
    {
        $id = $this->faker->uuid();
        $k = Knight::create($id, $this->faker->firstName(), 1, 2);

        $this->assertSame($id, $k->getId());
    }

    public function testGetPowerReturnsFloat(): void
    {
        $k = Knight::create('abc', 'A', 1, 2);
        $this->assertSame(3.0, $k->getPower());
        $this->assertIsFloat($k->getPower());
    }

    public function testNormalizeReturnsExpectedArray(): void
    {
        $id = $this->faker->uuid();
        $name = $this->faker->firstName();
        $strength = $this->faker->numberBetween(0, 100);
        $weaponPower = $this->faker->numberBetween(0, 100);

        $knight = Knight::create($id, $name, $strength, $weaponPower);

        $this->assertSame(
            [
                'id' => $id,
                'name' => $name,
                'strength' => $strength,
                'weapon_power' => $weaponPower,
            ],
            $knight->normalize()
        );
    }

    public function testDenormalizeBuildsKnightFromArray(): void
    {
        $id = $this->faker->uuid();
        $name = $this->faker->firstName();
        $strength = $this->faker->numberBetween(0, 100);
        $weaponPower = $this->faker->numberBetween(0, 100);

        $knight = Knight::denormalize([
            'id' => $id,
            'name' => $name,
            'strength' => $strength,
            'weapon_power' => $weaponPower,
        ]);

        $this->assertSame($id, $knight->getId());
        $this->assertSame($name, $knight->name);
        $this->assertSame($strength, $knight->strength);
        $this->assertSame($weaponPower, $knight->weaponPower);
    }
}
