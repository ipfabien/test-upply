<?php

declare(strict_types=1);

namespace App\Domain;

interface Fighter
{
    public function getId(): string;

    public function getPower(): float;
}
