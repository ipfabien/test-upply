<?php

declare(strict_types=1);

namespace App\Infrastructure\Id;

use App\Shared\Id\IdGenerator;
use Symfony\Component\Uid\Uuid;

final class SymfonyUuidIdGenerator implements IdGenerator
{
    public function generate(): string
    {
        return Uuid::v4()->toRfc4122();
    }
}
