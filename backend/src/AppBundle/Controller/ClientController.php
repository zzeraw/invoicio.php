<?php

namespace App\AppBundle\Controller;

use App\AppBundle\Security\CurrentUserResolver;
use App\InvoiceBundle\PublicInterface\ClientDtoInterface;
use App\InvoiceBundle\PublicService\ClientDtoFactoryInterface;
use App\InvoiceBundle\PublicService\ClientManageServiceInterface;
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
        private readonly ClientManageServiceInterface $clientManageService,
        private readonly ClientDtoFactoryInterface $clientDtoFactory,
        private readonly CurrentUserResolver $currentUserResolver
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
        $userId = $this->currentUserResolver->getUserId();
        $clients = $this->clientManageService->listForUser($userId);

        $payload = array_map([$this, 'normalizeClient'], $clients);

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
        $userId = $this->currentUserResolver->getUserId();
        $client = $this->clientManageService->getForUser($userId, $id);

        if (null === $client) {
            return new JsonResponse(['message' => 'Client not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->normalizeClient($client));
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
            $name = $this->getOptionalString($data, 'name');
            if (null === $name || '' === $name) {
                return new JsonResponse(['message' => 'Name is required.'], Response::HTTP_BAD_REQUEST);
            }

            $input = $this->clientDtoFactory->createCreateInput(
                $name,
                $this->getOptionalString($data, 'legal_address'),
                $this->getOptionalString($data, 'country_code'),
                $this->getOptionalString($data, 'tax_id'),
                $this->getOptionalString($data, 'tax_kpp'),
                $this->getOptionalString($data, 'registration_number'),
                $this->getOptionalArray($data, 'legal_details')
            );
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $userId = $this->currentUserResolver->getUserId();
        $client = $this->clientManageService->createForUser($userId, $input);

        return new JsonResponse($this->normalizeClient($client), Response::HTTP_CREATED);
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

            $input = $this->clientDtoFactory->createUpdateInput(
                $this->getOptionalString($data, 'name'),
                $this->getOptionalString($data, 'legal_address'),
                $this->getOptionalString($data, 'country_code'),
                $this->getOptionalString($data, 'tax_id'),
                $this->getOptionalString($data, 'tax_kpp'),
                $this->getOptionalString($data, 'registration_number'),
                $this->getOptionalArray($data, 'legal_details'),
                $this->getFieldFlags($data)
            );
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $userId = $this->currentUserResolver->getUserId();
        try {
            $client = $this->clientManageService->updateForUser($userId, $id, $input);
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        if (null === $client) {
            return new JsonResponse(['message' => 'Client not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->normalizeClient($client));
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
        $userId = $this->currentUserResolver->getUserId();
        $deleted = $this->clientManageService->deleteForUser($userId, $id);

        if (true !== $deleted) {
            return new JsonResponse(['message' => 'Client not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeClient(ClientDtoInterface $client): array
    {
        return [
            'id' => $client->getId(),
            'user_id' => $client->getUserId(),
            'name' => $client->getName(),
            'legal_address' => $client->getLegalAddress(),
            'country_code' => $client->getCountryCode(),
            'tax_id' => $client->getTaxId(),
            'tax_kpp' => $client->getTaxKpp(),
            'registration_number' => $client->getRegistrationNumber(),
            'legal_details' => $client->getLegalDetails(),
        ];
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

    /**
     * @param array<string, mixed> $data
     */
    private function getOptionalString(array $data, string $key): ?string
    {
        if (!array_key_exists($key, $data)) {
            return null;
        }

        $value = $data[$key];
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be a string.', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    private function getOptionalArray(array $data, string $key): ?array
    {
        if (!array_key_exists($key, $data)) {
            return null;
        }

        $value = $data[$key];
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be an array.', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, bool>
     */
    private function getFieldFlags(array $data): array
    {
        $fields = [];
        foreach (['name', 'legal_address', 'country_code', 'tax_id', 'tax_kpp', 'registration_number', 'legal_details'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[$this->toCamelCase($field)] = true;
            }
        }

        return $fields;
    }

    private function toCamelCase(string $value): string
    {
        $parts = explode('_', $value);
        $camel = array_shift($parts) ?: '';
        foreach ($parts as $part) {
            $camel .= ucfirst($part);
        }

        return $camel;
    }
}
