<?php

declare(strict_types=1);

namespace App\Controller;

use App\Shared\Normalization\Normalizable;
use Webmozart\Assert\Assert;

final class KnightRequest implements Normalizable
{
    public function __construct(
        public readonly string $name,
        public readonly int $strength,
        public readonly int $weaponPower,
    ) {
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function denormalize(array $data): static
    {
        Assert::keyExists($data, 'name', 'Missing field: name');
        Assert::stringNotEmpty($data['name'], 'Name cannot be empty');

        Assert::keyExists($data, 'strength', 'Missing field: strength');
        Assert::integerish($data['strength'], 'Strength must be an integer');

        Assert::keyExists($data, 'weapon_power', 'Missing field: weapon_power');
        Assert::integerish($data['weapon_power'], 'Weapon power must be an integer');

        return new self(
            (string) $data['name'],
            (int) $data['strength'],
            (int) $data['weapon_power'],
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function normalize(): array
    {
        return [
            'name' => $this->name,
            'strength' => $this->strength,
            'weapon_power' => $this->weaponPower,
        ];
    }
}
