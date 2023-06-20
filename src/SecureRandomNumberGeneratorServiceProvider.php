<?php

namespace CreativeCrafts\SecureRandomNumberGenerator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SecureRandomNumberGeneratorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-secure-random-number-generator')
            ->hasConfigFile();
    }
}
