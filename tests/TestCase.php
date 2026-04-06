<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->configureProjectDatabase($app);

        return $app;
    }

    /**
     * The project ships with a MySQL baseline schema, so feature tests need to
     * reuse the local DB configuration instead of PHPUnit's default SQLite setup.
     */
    protected function configureProjectDatabase(Application $app): void
    {
        $env = $this->readEnvironmentFile(base_path('.env'));
        $connection = $env['DB_CONNECTION'] ?? 'mysql';

        $app['config']->set('database.default', $connection);
        $app['config']->set("database.connections.{$connection}.host", $env['DB_HOST'] ?? '127.0.0.1');
        $app['config']->set("database.connections.{$connection}.port", (int) ($env['DB_PORT'] ?? 3306));
        $app['config']->set("database.connections.{$connection}.database", $env['DB_DATABASE'] ?? null);
        $app['config']->set("database.connections.{$connection}.username", $env['DB_USERNAME'] ?? 'root');
        $app['config']->set("database.connections.{$connection}.password", $env['DB_PASSWORD'] ?? '');

        $app['db']->purge($connection);
        $app['db']->setDefaultConnection($connection);
    }

    /**
     * @return array<string, string>
     */
    protected function readEnvironmentFile(string $path): array
    {
        if (! is_file($path)) {
            return [];
        }

        $values = [];

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $values[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
        }

        return $values;
    }
}
