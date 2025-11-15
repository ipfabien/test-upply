<?php

declare(strict_types=1);

namespace App\Domain;

use App\Shared\Normalization\Normalizable;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<string, Knight>
 */
final class KnightSet implements IteratorAggregate, Countable, Normalizable
{
    /**
     * @var array<string, Knight>
     */
    private array $knights = [];

    public function __construct(Knight ...$knights)
    {
        foreach ($knights as $knight) {
            $this->knights[$knight->getId()] = $knight;
        }
    }

    /**
     * @return Traversable<string, Knight>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->knights);
    }

    public function count(): int
    {
        return \count($this->knights);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @phpstan-param array<int, array{
     *     id: string,
     *     name: string,
     *     strength: int,
     *     weapon_power: int
     * }> $data
     */
    public static function denormalize(array $data): static
    {
        $knights = [];
        foreach ($data as $item) {
            if (\is_array($item)) {
                $knights[] = Knight::denormalize($item);
            }
        }

        return new self(...$knights);
    }

    /**
     * @return array<string, mixed>
     *
     * @phpstan-return array<int, array{
     *     id: string,
     *     name: string,
     *     strength: int,
     *     weapon_power: int
     * }>
     */
    public function normalize(): array
    {
        $items = [];

        foreach ($this->knights as $knight) {
            $items[] = $knight->normalize();
        }

        return $items;
    }
}
