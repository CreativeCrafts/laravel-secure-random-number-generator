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
    /** This is the start of the random number range */
    'from_number_range' => 1000,
    /** This is the end of the random number range */
    'to_number_range' => 9999999,
    /** Prevent infinite recursion */
    'max_retries' => 100,
    /** Enable caching for the generated random numbers */
    'use_cache' => false,
    /** Cache time in seconds */
    'cache_time' => 60,
    /** Default pattern for generating random numbers */
    'default_pattern' => '#####',
    /** Maximum batch size for a single operation*/
    'max_batch_size' => 1000,
    /** Multiplier for candidate generation (higher means more candidates per batch) */
    'batch_candidate_multiplier' => 2,
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
## New Features
The Laravel Secure Random Number Generator package has been enhanced with several new methods and trait to provide more flexibility and functionality when generating secure random numbers.

## New Methods

### Batch Generation
```php
/**
 * Generates multiple secure random numbers within the configured range.
 *
 * @param  int  $count  Number of unique random numbers to generate
 * @return array<int> Array of unique secure random numbers
 *
 * @throws RuntimeException If unable to generate enough unique numbers
 */
public function generateBatch(int $count): array;
```

### Formatted Numbers
```php
/**
 * Generates a secure random number with optional prefix and suffix.
 *
 * @param  string  $prefix  String to prepend to the number
 * @param  string  $suffix  String to append to the number
 * @return string Formatted random number with prefix and suffix
 */
public function generateFormatted(string $prefix = '', string $suffix = ''): string;
```

### Pattern-Based Numbers
```php
/**
 * Generates a secure random number formatted according to a pattern.
 *
 * The pattern uses # as a placeholder for each digit.
 * Example: "###-###" might produce "123-456"
 *
 * @param  string  $pattern  Format pattern with # as digit placeholders
 * @return string Formatted random number
 *
 * @throws RuntimeException If pattern is invalid or number generation fails
 */
public function generateWithPattern(string $pattern): string;
```

### Batch Formatted Numbers
```php
/**
 * Generates multiple secure random numbers with optional prefix and suffix.
 *
 * @param  int  $count  Number of unique random numbers to generate
 * @param  string  $prefix  String to prepend to each number
 * @param  string  $suffix  String to append to each number
 * @return array<string> Array of formatted unique secure random numbers
 *
 * @throws RuntimeException If unable to generate enough unique numbers
 */
public function generateBatchFormatted(int $count, string $prefix = '', string $suffix = ''): array;
```

### Batch Pattern-Based Numbers
```php
/**
 * Generates multiple secure random numbers formatted according to a pattern.
 *
 * @param  int  $count  Number of unique random numbers to generate
 * @param  string  $pattern  Format pattern with # as digit placeholders
 * @return array<string> Array of formatted unique secure random numbers
 *
 * @throws RuntimeException If unable to generate enough unique numbers
 */
public function generateBatchWithPattern(int $count, string $pattern): array;
```

### Range Customization
```php
/**
 * Sets the minimum value for the random number range.
 *
 * @param  int $min  The minimum value (inclusive)
 */
public function min(int $min): self;

/**
 * Sets the maximum value for the random number range.
 *
 * @param  int $max  The maximum value (inclusive)
 */
public function max(int $max): self;
```

### Uniqueness Validation
```php
/**
 * Sets the table and column for uniqueness validation.
 *
 * @param  string  $table  The database table name
 * @param  string  $column  The column name in the table
 */
public function uniqueIn(string $table, string $column): self;
```

## Usage

### Generating Multiple Random Numbers
```php
// Generate 5 unique random numbers
$numbers = SecureRandomNumberGenerator::useDefaultConfigNumberRange()
    ->generateBatch(5);
// Result: [12345, 67890, 54321, 98765, 13579]
```

### Generating Formatted Numbers
```php
// Generate a random number with a specific pattern
$phoneNumber = SecureRandomNumberGenerator::setNumberRange(0, 9)
    ->generateWithPattern('###-###-####');
// Result: "123-456-7890"
```

### Generating Multiple Formatted Numbers
```php
// Generate 3 unique invoice numbers
$invoiceNumbers = SecureRandomNumberGenerator::useDefaultConfigNumberRange()
    ->generateBatchFormatted(3, 'INV-', '/2023');
// Result: ["INV-12345/2023", "INV-67890/2023", "INV-54321/2023"]
```

### Generating Multiple Pattern-Based Numbers
```php
// Generate 3 unique product codes
$productCodes = SecureRandomNumberGenerator::setNumberRange(0, 9)
    ->generateBatchWithPattern(3, 'PRD-###-###');
// Result: ["PRD-123-456", "PRD-789-012", "PRD-345-678"]
```

### Customizing Number Range
```php
// Generate a random number between 1000 and 9999
$pinCode = SecureRandomNumberGenerator::useDefaultConfigNumberRange()
    ->min(1000)
    ->max(9999)
    ->generate();
// Result: 4567
```

### Ensuring Uniqueness in Database
```php
// Generate a unique product code that doesn't exist in the database
$productCode = SecureRandomNumberGenerator::setNumberRange(10000, 99999)
    ->uniqueIn('products', 'product_code')
    ->generate();
// Result: 45678 (guaranteed to be unique in the products.product_code column)
```

## Fluent Interface
All methods can be chained for a fluent interface:

```php
$result = SecureRandomNumberGenerator::setNumberRange(1, 999)
    ->uniqueIn('orders', 'order_number')
    ->min(100)
    ->max(999)
    ->generateFormatted('ORD-', '-2023');
// Result: "ORD-123-2023" (unique in orders.order_number)
```

## Error Handling
The batch generation methods will throw a RuntimeException if they cannot generate the requested number of unique values after a reasonable number of attempts. 
Make sure to handle this exception in your code:

```php
try {
    $numbers = SecureRandomNumberGenerator::setNumberRange(1, 10)
        ->generateBatch(20); // Trying to generate 20 unique numbers from a range of only 10 possibilities
} catch (RuntimeException $e) {
    // Handle the error - perhaps by increasing the range or decreasing the count
    Log::error('Failed to generate unique numbers: ' . $e->getMessage());
}
```

## Trait

### HasSecureRandomNumber
This trait provides an easy way to automatically generate and assign secure random numbers to your Laravel Eloquent models.

## Usage

### Basic Usage
Add the trait to any Eloquent model where you want to automatically generate a secure random number:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use CreativeCrafts\SecureRandomNumberGenerator\Traits\HasSecureRandomNumber;

class Order extends Model
{
    use HasSecureRandomNumber;
    
    // The rest of your model...
}
```

By default, the trait will:
 -Generate a secure random number when creating a new model instance
 -Store the number in a column named reference_number
 -Use the default number range from the package configuration

### Customizing the Column Name
If you want to use a different column name for storing the random number, define the $randomNumberColumn property in your model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use CreativeCrafts\SecureRandomNumberGenerator\Traits\HasSecureRandomNumber;

class Order extends Model
{
    use HasSecureRandomNumber;
    
    /**
     * The column that will store the random number.
     *
     * @var string
     */
    protected $randomNumberColumn = 'order_number';
    
    // The rest of your model...
}
```

### Database Migration
Make sure to include the appropriate column in your migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique(); // Default column name
            // or
            $table->string('order_number')->unique(); // Custom column name
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
```

### Manually Generating Random Numbers
You can also manually generate a random number for a model:

```php
$randomNumber = Order::generateRandomNumber();
```

How It Works
The trait:
   1. Registers a creating event listener on the model
   2. When a new model is being created, it checks if the random number column is empty
   3. If empty, it generates a secure random number that is unique within the model's table
   4. Assigns the generated number to the specified column

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
