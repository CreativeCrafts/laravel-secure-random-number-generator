<?php

namespace CreativeCrafts\SecureRandomNumberGenerator\Tests;

use CreativeCrafts\SecureRandomNumberGenerator\SecureRandomNumberGeneratorServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'CreativeCrafts\\SecureRandomNumberGenerator\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            SecureRandomNumberGeneratorServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-secure-random-number-generator_table.php.stub';
        $migration->up();
        */
    }
}
