<?php

declare(strict_types=1);

namespace App\Shared\Normalization;

interface Normalizable
{
    /**
     * @param array<mixed> $data
     */
    public static function denormalize(array $data): static;

    /**
     * @return array<mixed>
     */
    public function normalize(): array;
}
