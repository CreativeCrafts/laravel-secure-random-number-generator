<?php

namespace CreativeCrafts\LaravelSecureRandomNumberGenerator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CreativeCrafts\LaravelSecureRandomNumberGenerator\LaravelSecureRandomNumberGenerator
 */
class LaravelSecureRandomNumberGenerator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \CreativeCrafts\LaravelSecureRandomNumberGenerator\LaravelSecureRandomNumberGenerator::class;
    }
}
