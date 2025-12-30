<?php

namespace App\AppBundle\Controller;

use App\AppBundle\Service\ClientManageService;
use InvalidArgumentException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/app/v1/clients')]
final class ClientController
{
    public function __construct(
        private readonly ClientManageService $clientManageService
    ) {
    }

    #[OA\Get(
        path: '/app/v1/clients',
        summary: 'List clients',
        tags: ['Clients']
    )]
    #[OA\Response(
        response: 200,
        description: 'Clients list'
    )]
    #[Route('', name: 'app_clients_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $payload = $this->clientManageService->listForCurrentUser();

        return new JsonResponse($payload);
    }

    #[OA\Get(
        path: '/app/v1/clients/{id}',
        summary: 'Get client',
        tags: ['Clients']
    )]
    #[OA\Response(
        response: 200,
        description: 'Client data'
    )]
    #[OA\Response(
        response: 404,
        description: 'Client not found'
    )]
    #[Route('/{id}', name: 'app_clients_get', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $client = $this->clientManageService->getForCurrentUser($id);

        if (null === $client) {
            return new JsonResponse(['message' => 'Client not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($client);
    }

    #[OA\Post(
        path: '/app/v1/clients',
        summary: 'Create client',
        tags: ['Clients']
    )]
    #[OA\Response(
        response: 201,
        description: 'Client created'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation error'
    )]
    #[Route('', name: 'app_clients_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = $this->getPayload($request);
            $client = $this->clientManageService->createForCurrentUser($data);
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($client, Response::HTTP_CREATED);
    }

    #[OA\Put(
        path: '/app/v1/clients/{id}',
        summary: 'Update client',
        tags: ['Clients']
    )]
    #[OA\Response(
        response: 200,
        description: 'Client updated'
    )]
    #[OA\Response(
        response: 404,
        description: 'Client not found'
    )]
    #[Route('/{id}', name: 'app_clients_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $data = $this->getPayload($request);
            $client = $this->clientManageService->updateForCurrentUser($id, $data);
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        if (null === $client) {
            return new JsonResponse(['message' => 'Client not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($client);
    }

    #[OA\Delete(
        path: '/app/v1/clients/{id}',
        summary: 'Delete client',
        tags: ['Clients']
    )]
    #[OA\Response(
        response: 204,
        description: 'Client deleted'
    )]
    #[OA\Response(
        response: 404,
        description: 'Client not found'
    )]
    #[Route('/{id}', name: 'app_clients_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $deleted = $this->clientManageService->deleteForCurrentUser($id);

        if (true !== $deleted) {
            return new JsonResponse(['message' => 'Client not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @return array<string, mixed>
     */
    private function getPayload(Request $request): array
    {
        $payload = (string) $request->getContent();
        if ('' === $payload) {
            throw new InvalidArgumentException('JSON payload is required.');
        }

        $data = json_decode($payload, true);
        if (!is_array($data)) {
            throw new InvalidArgumentException('Invalid JSON payload.');
        }

        return $data;
    }
}
