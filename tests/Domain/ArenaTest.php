<?php

declare(strict_types=1);

/**
 * This file is part of a Upply project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Domain;

use App\Domain\Arena;
use App\Domain\Fighter;
use PHPUnit\Framework\TestCase;

class ArenaTest extends TestCase
{
    public function testFight(): void
    {
        $orc1 = new Orc("1", 10);
        $orc2 = new Orc("2", 20);

        $arena = new Arena();
        $result = $arena->fight($orc1, $orc2);

        $this->assertNotNull($result);
        $this->assertEquals($orc1, $result);
    }

    public function testFightDraw(): void
    {
        $orc1 = new Orc("1", 10);
        $orc2 = new Orc("2", 10);

        $arena = new Arena();
        $result = $arena->fight($orc1, $orc2);

        $this->assertNull($result);
    }
}

class Orc implements Fighter
{
    private string $id;
    private float $strength;

    public function __construct(string $id, float $strength)
    {
        $this->id = $id;
        $this->strength = $strength;
    }

    public function getID(): string
    {
        return $this->id;
    }

    public function getPower(): float
    {
        return $this->strength;
    }
}
