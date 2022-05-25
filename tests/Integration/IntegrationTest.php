<?php

declare(strict_types=1);

namespace JBernavaPrah\LighthouseTranslation\Tests\Integration;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JBernavaPrah\LighthouseTranslation\LighthouseTranslationServiceProvider;
use Nuwave\Lighthouse\LighthouseServiceProvider;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;
use Orchestra\Testbench\TestCase;

abstract class IntegrationTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;
    use RefreshesSchemaCache;

    /**
     * @param Application $app
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            LighthouseServiceProvider::class,
            LighthouseTranslationServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
    }

    /**
     * @param Application $app
     */
    protected function defineEnvironment($app): void
    {

    }

    protected function getStubsPath(string $path): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $path;
    }

    protected function getAppKey(): string
    {
        /** @var string $appKey */
        $appKey = $this->app['config']->get('app.key');

        return $appKey;
    }
}