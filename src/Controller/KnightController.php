<?php

declare(strict_types=1);

namespace App\Controller;

use App\Handler\Command\CreateKnightCommand;
use App\Handler\Command\CreateKnightCommandHandler;
use App\Handler\Query\GetKnightQuery;
use App\Handler\Query\GetKnightQueryHandler;
use App\Handler\Query\ListKnightsQuery;
use App\Handler\Query\ListKnightsQueryHandler;
use App\Shared\Http\ApiEndpoint;
use App\Shared\Id\IdGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[ApiEndpoint]
class KnightController extends AbstractController
{
    public function __construct(
        private CreateKnightCommandHandler $createKnightHandler,
        private UrlGeneratorInterface $urlGenerator,
        private IdGenerator $idGenerator,
        private GetKnightQueryHandler $getKnightHandler,
        private ListKnightsQueryHandler $listKnightsHandler,
    ) {
    }

    #[Route('/knight', name: 'knight_create', methods: ['POST'])]
    public function create(KnightRequest $dto): JsonResponse
    {
        $externalId = $this->idGenerator->generate();

        $this->createKnightHandler->handle(
            new CreateKnightCommand(
                $externalId,
                $dto->name,
                $dto->strength,
                $dto->weaponPower,
            ),
        );

        return new JsonResponse(
            new \stdClass(),
            Response::HTTP_CREATED,
            ['Location' => $this->urlGenerator->generate('knight_get', ['id' => $externalId])]
        );
    }

    #[Route('/knight', name: 'knight_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $set = $this->listKnightsHandler->ask(new ListKnightsQuery());
        $items = [];
        foreach ($set as $externalId => $knight) {
            $items[] = [
                'id' => $externalId,
                'name' => $knight->name,
                'strength' => $knight->strength,
                'weapon_power' => $knight->weaponPower,
            ];
        }
        return new JsonResponse($items, Response::HTTP_OK);
    }

    #[Route('/knight/{id}', name: 'knight_get', methods: ['GET'])]
    public function getOne(string $id): JsonResponse
    {
        $knight = $this->getKnightHandler->ask(new GetKnightQuery($id));

        return new JsonResponse([
            'id' => $knight->getId(),
            'name' => $knight->name,
            'strength' => $knight->strength,
            'weapon_power' => $knight->weaponPower,
        ], Response::HTTP_OK);
    }
}
