<?php

declare(strict_types=1);

namespace App\Domain;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<string, Knight>
 */
final class KnightSet implements IteratorAggregate, Countable
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
}
