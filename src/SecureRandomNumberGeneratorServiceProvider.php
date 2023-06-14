<?php

namespace CreativeCrafts\SecureRandomNumberGenerator;

use CreativeCrafts\SecureRandomNumberGenerator\Commands\SecureRandomNumberGeneratorCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SecureRandomNumberGeneratorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-secure-random-number-generator')
            ->hasMigration('create_laravel-secure-random-number-generator_table')
            ->hasCommand(SecureRandomNumberGeneratorCommand::class);
    }
}
