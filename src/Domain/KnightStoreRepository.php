<?php

declare(strict_types=1);

namespace App\Domain;

use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\RuntimeException;

interface KnightStoreRepository
{
    /**
     * @throws BadRequestException
     * @throws RuntimeException
     */
    public function save(CreateKnight $createKnight): void;
}
