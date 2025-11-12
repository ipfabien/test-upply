<?php

declare(strict_types=1);

namespace App\Domain;

use App\Shared\Exception\NotFoundException;
use App\Shared\Exception\RuntimeException;

interface KnightProviderRepository
{
    /**
     * @throws NotFoundException
     * @throws RuntimeException
     */
    public function find(string $id): Knight;

    public function getAll(): KnightSet;
}
