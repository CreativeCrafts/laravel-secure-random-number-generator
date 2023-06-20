# laravel-secure-random-number-generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/creativecrafts/laravel-secure-random-number-generator.svg?style=flat-square)](https://packagist.org/packages/creativecrafts/laravel-secure-random-number-generator)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/creativecrafts/laravel-secure-random-number-generator/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/creativecrafts/laravel-secure-random-number-generator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/creativecrafts/laravel-secure-random-number-generator/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/creativecrafts/laravel-secure-random-number-generator/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/creativecrafts/laravel-secure-random-number-generator.svg?style=flat-square)](https://packagist.org/packages/creativecrafts/laravel-secure-random-number-generator)


 A handy package to generate a secure unique random number for a model.

## Installation

You can install the package via composer:

```bash
composer require creativecrafts/laravel-secure-random-number-generator
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="secure-random-number-generator-config"
```

This is the contents of the published config file:

```php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Number Range
    |--------------------------------------------------------------------------
    |
    | This option controls the default number range that will be used
    | when generating a random number.
    |
    */

    'from_number_range' => 1,
    'to_number_range' => 9999999,
];

```


## Usage

```php
use CreativeCrafts\SecureRandomNumberGenerator\SecureRandomNumberGenerator;
 // Generate a random unique number for a model
 // To make the number unique for a model, the package uses the model's table name and column name to check if the number is unique in the model's table
 // The from and to number range can be empty and the package will use the default number range set in the configuration file
 $tableName = 'users'
 $tableColumn = 'registration_number';
 $fromNumberRange = 100;
 $toNumberRange = 9999999;
 
 $secureUniqueRandomNumber = SecureRandomNumberGenerator::forModel($fromNumberRange, $toNumberRange, $tableName, $tableColumn,)->generate();
 
 // Generate a random number with a default value set in the configuration file
 $secureRandomNumber = SecureRandomNumberGenerator::useDefaultConfigNumberRange()->generate();
 
 // Generate a random number with a custom number range
 $fromNumberRange = 100;
 $toNumberRange = 9999999;
 $secureRandomNumber = SecureRandomNumberGenerator::setNumberRange($fromNumberRange, $toNumberRange)->generate();
 
 // Generate a random unique number for a model using the default number range set in the configuration file
 $tableName = 'users'
 $tableColumn = 'registration_number';
 
 $secureUniqueRandomNumber = SecureRandomNumberGenerator::forModelUsingDefaultConfigNumberRange($tableName, $tableColumn)->generate();

```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Godspower Oduose](https://github.com/rockblings)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
