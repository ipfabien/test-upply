<?php

declare(strict_types=1);

namespace App\Shared\Id;

interface IdGenerator
{
    public function generate(): string;
}
