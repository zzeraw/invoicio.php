<?php

namespace App\ApiBundle\Controller;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class HealthController
{
    #[OA\Get(
        path: '/api/v1/health',
        summary: 'Health check',
        tags: ['Health']
    )]
    #[OA\Response(
        response: 200,
        description: 'Service is healthy'
    )]
    #[Route('/api/v1/health', name: 'api_health', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
