<?php

declare(strict_types=1);

namespace App\Handler\Command;

use App\Domain\CreateKnight;
use App\Domain\KnightStoreRepository;
use App\Shared\Exception\BadRequestException;
use InvalidArgumentException;

final class CreateKnightCommandHandler
{
    public function __construct(private KnightStoreRepository $store)
    {
    }

    /**
     * @throws BadRequestException
     */
    public function handle(CreateKnightCommand $command): void
    {
        try {
            $this->store->save(
                CreateKnight::create(
                    $command->externalId,
                    $command->name,
                    $command->strength,
                    $command->weaponPower
                )
            );
        } catch (InvalidArgumentException $e) {
            throw new BadRequestException($e->getMessage(), $e);
        }
    }
}
