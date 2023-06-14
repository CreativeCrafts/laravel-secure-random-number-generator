<?php

namespace CreativeCrafts\LaravelSecureRandomNumberGenerator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use CreativeCrafts\LaravelSecureRandomNumberGenerator\Commands\LaravelSecureRandomNumberGeneratorCommand;

class LaravelSecureRandomNumberGeneratorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-secure-random-number-generator')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-secure-random-number-generator_table')
            ->hasCommand(LaravelSecureRandomNumberGeneratorCommand::class);
    }
}
