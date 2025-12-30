<?php

namespace App\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class SwaggerDocTest extends KernelTestCase
{
    /**
     * Проверяет, что Swagger UI для AppBundle доступен.
     */
    public function testSwaggerUiIsAvailable(): void
    {
        $kernel = self::bootKernel();
        $response = $kernel->handle(Request::create('/app/v1/doc', 'GET'));

        $contentType = (string) $response->headers->get('content-type');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('text/html', $contentType);
    }

    protected function tearDown(): void
    {
        self::$kernel?->shutdown();
        self::$kernel = null;
        self::$booted = false;
    }

    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }
}
