<?php

namespace App\ApiBundle\Tests\Controller;

use App\ApiBundle\Controller\HealthController;
use PHPUnit\Framework\TestCase;

final class HealthControllerTest extends TestCase
{
    /**
     * Проверяет, что health-эндпоинт внешнего API возвращает статус ok.
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
