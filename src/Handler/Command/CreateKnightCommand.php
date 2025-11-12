<?php

declare(strict_types=1);

namespace App\Handler\Command;

final class CreateKnightCommand
{
    public function __construct(
        public readonly string $externalId,
        public readonly string $name,
        public readonly int $strength,
        public readonly int $weaponPower,
    ) {
    }
}
