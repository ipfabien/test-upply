<?php

declare(strict_types=1);

namespace App\Domain;

final class Arena
{
    public function fight(Fighter $fighterA, Fighter $fighterB): ?Fighter
    {
        $powerA = $fighterA->getPower();
        $powerB = $fighterB->getPower();

        if ($powerA === $powerB) {
            return null;
        }

        return $powerA > $powerB ? $fighterA : $fighterB;
    }
}
