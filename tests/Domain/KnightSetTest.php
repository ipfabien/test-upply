<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Knight;
use App\Domain\KnightSet;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class KnightSetTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    public function testIterationKeysAreExternalIdsAndOrderIsInsertion(): void
    {
        $k1 = Knight::create('id-1', 'A', 1, 1);
        $k2 = Knight::create('id-2', 'B', 2, 2);
        $k3 = Knight::create('id-3', 'C', 3, 3);

        $set = new KnightSet($k1, $k2, $k3);
        $keys = [];
        foreach ($set as $id => $k) {
            $keys[] = $id;
        }

        $this->assertSame(['id-1', 'id-2', 'id-3'], $keys);
    }

    public function testNormalizeReturnsArrayOfKnights(): void
    {
        $k1 = Knight::create(
            $this->faker->uuid(),
            $this->faker->firstName(),
            $this->faker->numberBetween(0, 100),
            $this->faker->numberBetween(0, 100),
        );
        $k2 = Knight::create(
            $this->faker->uuid(),
            $this->faker->firstName(),
            $this->faker->numberBetween(0, 100),
            $this->faker->numberBetween(0, 100),
        );

        $set = new KnightSet($k1, $k2);
        $data = $set->normalize();

        $this->assertCount(2, $data);
        $this->assertSame($k1->getId(), $data[0]['id']);
        $this->assertSame($k2->getId(), $data[1]['id']);
    }

    public function testDenormalizeBuildsKnightSetFromArray(): void
    {
        $payload = [];
        for ($i = 0; $i < 3; $i++) {
            $payload[] = [
                'id' => $this->faker->uuid(),
                'name' => $this->faker->firstName(),
                'strength' => $this->faker->numberBetween(0, 100),
                'weapon_power' => $this->faker->numberBetween(0, 100),
            ];
        }

        $set = KnightSet::denormalize($payload);

        $this->assertInstanceOf(KnightSet::class, $set);
        $this->assertCount(3, $set);

        $ids = [];
        foreach ($set as $id => $knight) {
            $ids[] = $id;
        }

        $this->assertSame(
            array_column($payload, 'id'),
            $ids
        );
    }
}
