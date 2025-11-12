<?php

declare(strict_types=1);

namespace App\Handler\Query;

use App\Domain\KnightProviderRepository;
use App\Domain\KnightSet;

final class ListKnightsQueryHandler
{
    public function __construct(private KnightProviderRepository $provider)
    {
    }

    public function ask(ListKnightsQuery $query): KnightSet
    {
        /** @var KnightSet $set */
        $set = $this->provider->getAll();

        return $set;
    }
}
