<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Knight;
use PHPUnit\Framework\TestCase;

final class KnightTest extends TestCase
{
    public function testGetIdReturnsExternalId(): void
    {
        $k = Knight::create('abc', 'A', 1, 2);
        $this->assertSame('abc', $k->getId());
    }

    public function testGetPowerReturnsFloat(): void
    {
        $k = Knight::create('abc', 'A', 1, 2);
        $this->assertSame(3.0, $k->getPower());
        $this->assertIsFloat($k->getPower());
    }
}
