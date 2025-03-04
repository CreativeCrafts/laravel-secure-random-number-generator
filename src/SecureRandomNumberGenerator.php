<?php

declare(strict_types=1);

namespace CreativeCrafts\SecureRandomNumberGenerator;

use CreativeCrafts\SecureRandomNumberGenerator\Contracts\SecureRandomNumberGeneratorContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Random\Engine\Secure;
use Random\Randomizer;
use RuntimeException;

class SecureRandomNumberGenerator implements SecureRandomNumberGeneratorContract
{
    public function __construct(
        protected int $fromNumberRange,
        protected int $toNumberRange,
        protected string $tableName = '',
        protected string $tableColumnName = ''
    ) {
    }

    /**
     * Creates a new instance using the default number range configuration.
     *
     * This method initializes a SecureRandomNumberGenerator with the default
     * minimum and maximum values from the configuration, without specifying
     * a table name or column for uniqueness validation.
     *
     * @return self A new instance of SecureRandomNumberGenerator
     */
    public static function useDefaultConfigNumberRange(): self
    {
        return new self(self::fromRangeNumber(), self::toRangeNumber(), '', '');
    }

    /**
     * Creates a new instance with custom number range configuration.
     *
     * This method initializes a SecureRandomNumberGenerator with the specified
     * minimum and maximum values, without specifying a table name or column
     * for uniqueness validation.
     *
     * @param  int  $fromNumberRange  The minimum value (inclusive) for the random number range
     * @param  int  $toNumberRange  The maximum value (inclusive) for the random number range
     * @return self A new instance of SecureRandomNumberGenerator
     */
    public static function setNumberRange(int $fromNumberRange, int $toNumberRange): self
    {
        return new self($fromNumberRange, $toNumberRange, '', '');
    }

    /**
     * Creates a new instance with custom number range configuration for model uniqueness validation.
     *
     * This method initializes a SecureRandomNumberGenerator with the specified
     * minimum and maximum values, and configures it to validate uniqueness against
     * a specific database table column.
     *
     * @param  int  $fromNumberRange  The minimum value (inclusive) for the random number range
     * @param  int  $toNumberRange  The maximum value (inclusive) for the random number range
     * @param  string  $tableName  The database table name to check for uniqueness
     * @param  string  $tableColumnName  The column name in the table to check for uniqueness
     * @return self A new instance of SecureRandomNumberGenerator
     */
    public static function forModel(int $fromNumberRange, int $toNumberRange, string $tableName, string $tableColumnName): self
    {
        return new self($fromNumberRange, $toNumberRange, $tableName, $tableColumnName);
    }

    /**
     * Creates a new instance using the default number range configuration for model uniqueness validation.
     *
     * This method initializes a SecureRandomNumberGenerator with the default
     * minimum and maximum values from the configuration, and configures it to validate
     * uniqueness against a specific database table column.
     *
     * @param  string  $tableName  The database table name to check for uniqueness
     * @param  string  $tableColumnName  The column name in the table to check for uniqueness
     * @return self A new instance of SecureRandomNumberGenerator
     */
    public static function forModelUsingDefaultConfigNumberRange(string $tableName, string $tableColumnName): self
    {
        return new self(self::fromRangeNumber(), self::toRangeNumber(), $tableName, $tableColumnName);
    }

    /**
     * Generates a secure random number within the configured range.
     *
     * This method creates a cryptographically secure random number between the
     * specified minimum and maximum values. If table name and column are provided,
     * it also validates that the generated number is unique in the specified
     * database table column. If the generated number already exists in the database,
     * it attempts to generate a new number up to a maximum number of retries.
     *
     * @return int A secure random number within the configured range that is unique
     *             in the database if table validation is configured
     *
     * @throws RuntimeException If unable to generate a unique number after maximum retries
     */
    public function generate(): int
    {
        $randomizer = new Randomizer(new Secure());
        $maxRetries = Config::integer(key: 'secure-random-number-generator.max_retries', default: 100);
        $attempts = 0;

        if (! isset($this->tableColumnName, $this->tableName) || $this->tableName === '' || $this->tableColumnName === '') {
            return $randomizer->getInt($this->fromNumberRange, $this->toNumberRange);
        }

        do {
            $attempts++;
            $generatedNumber = $randomizer->getInt($this->fromNumberRange, $this->toNumberRange);

            $exists = $this->numberExists($generatedNumber);
            if (! $exists) {
                return $generatedNumber;
            }

            // If we've tried too many times, either the range is too small or there's another issue
            if ($attempts >= $maxRetries) {
                throw new RuntimeException(
                    message: "Failed to generate a unique random number after {$maxRetries} attempts. ".
                    'Consider expanding your number range.'
                );
            }
        } while (true);
    }

    /**
     * Retrieves the minimum value for the random number range from configuration.
     *
     * This method fetches the configured minimum value (inclusive) that will be used
     * as the lower bound when generating random numbers. The value is read from the
     * 'secure-random-number-generator.from_number_range' configuration setting.
     *
     * @return int The minimum value (inclusive) for the random number range
     */
    protected static function fromRangeNumber(): int
    {
        return Config::integer(key: 'secure-random-number-generator.from_number_range', default: 1000);
    }

    /**
     * Retrieves the maximum value for the random number range from configuration.
     *
     * This method fetches the configured maximum value (inclusive) that will be used
     * as the upper bound when generating random numbers. The value is read from the
     * 'secure-random-number-generator.to_number_range' configuration setting.
     *
     * @return int The maximum value (inclusive) for the random number range
     */
    protected static function toRangeNumber(): int
    {
        return Config::integer(key: 'secure-random-number-generator.to_number_range', default: 9999999);
    }

    /**
     * Generates multiple secure random numbers within the configured range.
     *
     * @param  int  $count  Number of unique random numbers to generate
     * @return array<int> Array of unique secure random numbers
     *
     * @throws RuntimeException If unable to generate enough unique numbers
     */
    public function generateBatch(int $count): array
    {
        if ($count <= 0) {
            return [];
        }

        $randomizer = new Randomizer(new Secure());
        $maxRetries = Config::integer(key: 'secure-random-number-generator.max_retries', default: 100);
        $totalAttempts = 0;
        $uniqueNumbers = [];

        if (! isset($this->tableColumnName, $this->tableName) || $this->tableName === '' || $this->tableColumnName === '') {
            while (count($uniqueNumbers) < $count) {
                $uniqueNumbers[] = $randomizer->getInt($this->fromNumberRange, $this->toNumberRange);
                $uniqueNumbers = array_unique($uniqueNumbers);
            }

            return array_slice($uniqueNumbers, 0, $count);
        }

        while (count($uniqueNumbers) < $count) {
            $batchSize = ($count - count($uniqueNumbers)) * Config::integer(key: 'secure-random-number-generator.batch_candidate_multiplier', default: 2);
            $candidates = [];

            for ($i = 0; $i < $batchSize; $i++) {
                $candidates[] = $randomizer->getInt($this->fromNumberRange, $this->toNumberRange);
            }

            $candidates = array_unique($candidates);
            $existingNumbers = $this->findExistingNumbers($candidates);
            $newUniqueNumbers = array_diff($candidates, $existingNumbers);
            $uniqueNumbers = array_unique(array_merge($uniqueNumbers, $newUniqueNumbers));

            $totalAttempts++;
            if ($totalAttempts >= $maxRetries && count($uniqueNumbers) < $count) {
                throw new RuntimeException(
                    message: "Failed to generate {$count} unique random numbers after {$maxRetries} batch attempts. ".
                    'Consider expanding your number range.'
                );
            }
        }
        return array_slice($uniqueNumbers, 0, $count);
    }

    /**
     * Generates a secure random number with optional prefix and suffix.
     *
     * @param  string  $prefix  String to prepend to the number
     * @param  string  $suffix  String to append to the number
     * @return string Formatted random number with prefix and suffix
     */
    public function generateFormatted(string $prefix = '', string $suffix = ''): string
    {
        $number = $this->generate();

        return $prefix.$number.$suffix;
    }

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
    public function generateWithPattern(string $pattern): string
    {
        // Count how many digits we need
        $requiredDigits = substr_count(haystack: $pattern, needle: '#');

        if ($requiredDigits <= 0) {
            throw new RuntimeException(message: 'Pattern must contain at least one # character as a digit placeholder');
        }

        // Generate a number with enough digits
        $minRange = max($this->fromNumberRange, 10 ** ($requiredDigits - 1));
        $maxRange = min($this->toNumberRange, (10 ** $requiredDigits) - 1);

        // Create a temporary generator with adjusted range if needed
        $generator = $this;
        if ($minRange !== $this->fromNumberRange || $maxRange !== $this->toNumberRange) {
            $generator = new self($minRange, $maxRange, $this->tableName, $this->tableColumnName);
        }

        $number = $generator->generate();
        $numberStr = (string) $number;

        // Pad with leading zeros if necessary
        $numberStr = str_pad($numberStr, $requiredDigits, '0', STR_PAD_LEFT);

        // Replace placeholders with digits
        $result = $pattern;
        $digitIndex = 0;

        for ($i = 0, $iMax = strlen($result); $i < $iMax; $i++) {
            if (($result[$i] === '#') && $digitIndex < strlen($numberStr)) {
                $result[$i] = $numberStr[$digitIndex++];
            }
        }

        return $result;
    }

    /**
     * Checks if a number exists in the database with optional caching.
     *
     * @param  int  $number  The number to check
     * @return bool Whether the number exists
     */
    protected function numberExists(int $number): bool
    {
        $useCache = Config::boolean(key: 'secure-random-number-generator.use_cache', default: false);
        $cacheTime = Config::integer(key: 'secure-random-number-generator.cache_time', default: 60); // seconds

        if (! $useCache) {
            return DB::table($this->tableName)
                ->where($this->tableColumnName, $number)
                ->exists();
        }

        $cacheKey = "secure_random_number_exists:{$this->tableName}:{$this->tableColumnName}:{$number}";

        /** @var bool $numberExist */
        $numberExist = Cache::remember($cacheKey, $cacheTime, function () use ($number) {
            return DB::table($this->tableName)
                ->where($this->tableColumnName, $number)
                ->exists();
        });

        return $numberExist;
    }

    /**
     * Efficiently checks which numbers from a set already exist in the database.
     *
     * @param  array<int>  $numbers  Array of numbers to check
     * @return array<int> Array of numbers that already exist (converted to integers)
     */
    protected function findExistingNumbers(array $numbers): array
    {
        if (empty($numbers)) {
            return [];
        }

        $existingNumbers = DB::table($this->tableName)
            ->whereIn($this->tableColumnName, $numbers)
            ->pluck($this->tableColumnName)
            ->toArray();
        $result = [];

        /** @var int $value */
        foreach ($existingNumbers as $value) {
            $result[] = $value;
        }

        return $result;
    }

    /**
     * Sets the table and column for uniqueness validation.
     *
     * @param  string  $table  The database table name
     * @param  string  $column  The column name in the table
     */
    public function uniqueIn(string $table, string $column): self
    {
        $this->tableName = $table;
        $this->tableColumnName = $column;

        return $this;
    }

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
    public function generateBatchFormatted(int $count, string $prefix = '', string $suffix = ''): array
    {
        $numbers = $this->generateBatch($count);

        return array_map(static function ($number) use ($prefix, $suffix) {
            return $prefix.$number.$suffix;
        }, $numbers);
    }

    /**
     * Generates multiple secure random numbers formatted according to a pattern.
     *
     * @param  int  $count  Number of unique random numbers to generate
     * @param  string  $pattern  Format pattern with # as digit placeholders
     * @return array<string> Array of formatted unique secure random numbers
     *
     * @throws RuntimeException If unable to generate enough unique numbers
     */
    public function generateBatchWithPattern(int $count, string $pattern): array
    {
        $numbers = $this->generateBatch($count);
        $result = [];

        foreach ($numbers as $number) {
            $numberStr = (string) $number;
            $requiredDigits = substr_count($pattern, '#');

            // Pad with leading zeros if necessary
            $numberStr = str_pad($numberStr, $requiredDigits, '0', STR_PAD_LEFT);

            // Replace placeholders with digits
            $formattedNumber = $pattern;
            $digitIndex = 0;

            for ($i = 0, $iMax = strlen($formattedNumber); $i < $iMax; $i++) {
                if ($formattedNumber[$i] === '#' && $digitIndex < strlen($numberStr)) {
                    $formattedNumber[$i] = $numberStr[$digitIndex++];
                }
            }

            $result[] = $formattedNumber;
        }

        return $result;
    }

    /**
     * Sets the minimum value for the random number range.
     *
     * @param  int  $min  The minimum value (inclusive)
     */
    public function min(int $min): self
    {
        $this->fromNumberRange = $min;

        return $this;
    }

    /**
     * Sets the maximum value for the random number range.
     *
     * @param  int  $max  The maximum value (inclusive)
     */
    public function max(int $max): self
    {
        $this->toNumberRange = $max;

        return $this;
    }
}
