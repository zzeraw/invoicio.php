<?php

namespace App\AppBundle\Controller;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class HealthController
{
    #[OA\Get(
        path: '/api/health',
        summary: 'Health check',
        tags: ['Health']
    )]
    #[OA\Response(
        response: 200,
        description: 'Service is healthy'
    )]
    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
