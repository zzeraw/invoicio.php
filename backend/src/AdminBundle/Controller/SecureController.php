<?php

namespace App\AdminBundle\Controller;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class SecureController
{
    #[OA\Get(
        path: '/admin/v1/secure',
        summary: 'Secure check',
        tags: ['Security']
    )]
    #[OA\Response(
        response: 200,
        description: 'Authenticated'
    )]
    #[Route('/admin/v1/secure', name: 'admin_secure', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
