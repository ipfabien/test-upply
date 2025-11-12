<?php

declare(strict_types=1);

/**
 * This file is part of a Upply project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handler;

use App\Domain\Knight;
use App\Domain\KnightRepositoryInterface;

class KnightHandler
{
    private KnightRepositoryInterface $knightRepository;

    public function __construct(KnightRepositoryInterface $knightRepository)
    {
        $this->knightRepository = $knightRepository;
    }

    public function getKnight(string $id): Knight
    {
        return $this->knightRepository->find($id);
    }

    public function listKnights(): iterable
    {

    }

    public function fight($knightA, $knightB): Knight
    {

    }
}
