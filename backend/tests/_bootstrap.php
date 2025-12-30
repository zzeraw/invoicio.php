<?php

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->usePutenv(true)->overload(dirname(__DIR__) . '/.env.test');

$_SERVER['APP_ENV'] = $_SERVER['APP_ENV'] ?? 'test';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? '1';

$envPath = dirname(__DIR__) . '/.env.test';
$envVars = $dotenv->parse(file_get_contents($envPath), $envPath);
$databaseUrl = $envVars['DATABASE_URL'] ?? getenv('DATABASE_URL') ?: ($_SERVER['DATABASE_URL'] ?? ($_ENV['DATABASE_URL'] ?? null));
if ($databaseUrl === null) {
    throw new RuntimeException('DATABASE_URL is not set for tests.');
}

$databaseUrl = trim($databaseUrl, " \t\n\r\0\x0B\"'");
$parts = parse_url($databaseUrl);
if ($parts === false || !isset($parts['host'], $parts['path'])) {
    throw new RuntimeException('DATABASE_URL is invalid for tests.');
}

$user = $parts['user'] ?? '';
$pass = $parts['pass'] ?? '';
$port = isset($parts['port']) ? (int) $parts['port'] : 5432;
$dbName = ltrim($parts['path'], '/');
$query = [];
if (isset($parts['query'])) {
    parse_str($parts['query'], $query);
}

$connection = DriverManager::getConnection([
    'driver' => 'pdo_pgsql',
    'host' => $parts['host'],
    'port' => $port,
    'user' => $user,
    'password' => $pass,
    'dbname' => $dbName,
    'server_version' => $query['serverVersion'] ?? null,
]);

$connection->executeStatement('DROP SCHEMA public CASCADE');
$connection->executeStatement('CREATE SCHEMA public');

$schemaDir = dirname(__DIR__) . '/migrations/schema';
$schemaFiles = glob($schemaDir . '/*.sql') ?: [];
sort($schemaFiles);

foreach ($schemaFiles as $schemaFile) {
    $sql = trim((string) file_get_contents($schemaFile));
    if ($sql === '') {
        continue;
    }

    $statements = preg_split('/;\s*[\r\n]+/', $sql) ?: [];
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if ($statement === '') {
            continue;
        }
        $connection->executeStatement($statement);
    }
}
