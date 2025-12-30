<?php

namespace App\ApiBundle\Controller;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class SecureController
{
    #[OA\Get(
        path: '/api/v1/secure',
        summary: 'Secure check',
        tags: ['Security']
    )]
    #[OA\Response(
        response: 200,
        description: 'Authenticated'
    )]
    #[Route('/api/v1/secure', name: 'api_secure', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
