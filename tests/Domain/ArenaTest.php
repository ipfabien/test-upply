<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Arena;
use App\Domain\Fighter;
use PHPUnit\Framework\TestCase;

final class ArenaTest extends TestCase
{
    public function testFight(): void
    {
        $orc1 = new Orc('1', 10);
        $orc2 = new Orc('2', 20);

        $arena = new Arena();
        $result = $arena->fight($orc1, $orc2);

        $this->assertNotNull($result);
        $this->assertEquals($orc2, $result);
    }

    public function testFightDraw(): void
    {
        $orc1 = new Orc('1', 10);
        $orc2 = new Orc('2', 10);

        $arena = new Arena();
        $result = $arena->fight($orc1, $orc2);

        $this->assertNull($result);
    }
}

final class Orc implements Fighter
{
    public function __construct(private string $id, private float $strength)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPower(): float
    {
        return $this->strength;
    }
}
