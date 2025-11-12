<?php

declare(strict_types=1);

namespace App\Domain;

class Knight implements Fighter
{
    private function __construct(
        public readonly string $externalId,
        public readonly string $name,
        public readonly int $strength,
        public readonly int $weaponPower
    ) {
    }

    public static function create(string $externalId, string $name, int $strength, int $weaponPower): self
    {
        return new self($externalId, $name, $strength, $weaponPower);
    }

    public function getId(): string
    {
        return $this->externalId;
    }

    public function getPower(): float
    {
        return (float) ($this->strength + $this->weaponPower);
    }
}
