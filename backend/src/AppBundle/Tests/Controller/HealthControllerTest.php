<?php

namespace App\AppBundle\Tests\Controller;

use App\AppBundle\Controller\HealthController;
use PHPUnit\Framework\TestCase;

final class HealthControllerTest extends TestCase
{
    /**
     * Проверяет, что health-эндпоинт возвращает статус ok.
     */
    public function testHealthEndpointReturnsOk(): void
    {
        $controller = new HealthController();
        $response = $controller();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));
        $this->assertSame('{"status":"ok"}', $response->getContent());
    }
}
