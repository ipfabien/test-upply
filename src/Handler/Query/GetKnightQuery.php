<?php

declare(strict_types=1);

namespace App\Handler\Query;

final class GetKnightQuery
{
    public function __construct(public readonly string $externalId)
    {
    }
}
