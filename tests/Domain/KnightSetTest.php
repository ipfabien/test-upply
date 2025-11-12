<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Knight;
use App\Domain\KnightSet;
use PHPUnit\Framework\TestCase;

final class KnightSetTest extends TestCase
{
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
}
