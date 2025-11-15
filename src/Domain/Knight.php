<?php

declare(strict_types=1);

namespace App\Domain;

use App\Shared\Normalization\Normalizable;
use Webmozart\Assert\Assert;

final class Knight implements Fighter, Normalizable
{
    private function __construct(
        public readonly string $externalId,
        public readonly string $name,
        public readonly int $strength,
        public readonly int $weaponPower
    ) {
    }

    public static function create(string $externalId, string $name, int $strength, int $weaponPower): static
    {
        return new self($externalId, $name, $strength, $weaponPower);
    }

    /**
     * @param array{
     *     id: string,
     *     name: string,
     *     strength: int,
     *     weapon_power: int
     * } $data
     */
    public static function denormalize(array $data): static
    {
        Assert::keyExists($data, 'id', 'Missing field: id');
        Assert::keyExists($data, 'name', 'Missing field: name');
        Assert::keyExists($data, 'strength', 'Missing field: strength');
        Assert::keyExists($data, 'weapon_power', 'Missing field: weapon_power');

        return self::create(
            $data['id'],
            $data['name'],
            $data['strength'],
            $data['weapon_power'],
        );
    }

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     strength: int,
     *     weapon_power: int
     * }
     */
    public function normalize(): array
    {
        return [
            'id' => $this->externalId,
            'name' => $this->name,
            'strength' => $this->strength,
            'weapon_power' => $this->weaponPower,
        ];
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
