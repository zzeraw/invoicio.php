<?php

namespace App\ApiBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class SwaggerDocTest extends TestCase
{
    /**
     * Проверяет, что Swagger UI для ApiBundle доступен.
     */
    public function testSwaggerUiIsAvailable(): void
    {
        $routes = $this->loadRoutes();
        $paths = array_map(
            static fn (array $route): string => (string) ($route['path'] ?? ''),
            $routes
        );

        $this->assertContains('/api/v1/doc', $paths);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function loadRoutes(): array
    {
        $configPath = dirname(__DIR__, 4) . '/config/routes/nelmio_api_doc.yaml';
        $data = Yaml::parseFile($configPath);
        if (!is_array($data)) {
            $this->fail('Swagger routes config is invalid.');
        }

        return $data;
    }
}
