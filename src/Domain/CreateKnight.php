<?php

declare(strict_types=1);

namespace App\Domain;

use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

class CreateKnight
{
    private function __construct(
        public readonly string $externalId,
        public readonly string $name,
        public readonly int $strength,
        public readonly int $weaponPower,
    ) {
    }

    public static function create(string $externalId, string $name, int $strength, int $weaponPower): self
    {
        Assert::true(Uuid::isValid($externalId), 'External id must be a valid UUID.');
        Assert::stringNotEmpty($name, 'Knight name cannot be empty.');
        Assert::greaterThanEq($strength, 0, 'Knight strength must be >= 0.');
        Assert::greaterThanEq($weaponPower, 0, 'Knight weaponPower must be >= 0.');

        return new self($externalId, $name, $strength, $weaponPower);
    }
}
