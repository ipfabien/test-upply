<?php

declare(strict_types=1);

namespace App\Handler\Query;

use App\Domain\Knight;
use App\Domain\KnightProviderRepository;
use App\Shared\Exception\NotFoundException;
use App\Shared\Exception\RuntimeException;

final class GetKnightQueryHandler
{
    public function __construct(private KnightProviderRepository $provider)
    {
    }

    /**
     * @throws NotFoundException
     * @throws RuntimeException
     */
    public function ask(GetKnightQuery $query): Knight
    {
        return $this->provider->find($query->externalId);
    }
}
