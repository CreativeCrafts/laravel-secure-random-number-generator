<?php

declare(strict_types=1);

namespace CreativeCrafts\SecureRandomNumberGenerator\Contracts;

use RuntimeException;

interface SecureRandomNumberGeneratorContract
{
    /**
     * Creates a new instance using the default number range configuration.
     *
     * This method initializes a SecureRandomNumberGenerator with the default
     * minimum and maximum values from the configuration, without specifying
     * a table name or column for uniqueness validation.
     *
     * @return self A new instance of SecureRandomNumberGenerator
     */
    public static function useDefaultConfigNumberRange(): self;

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
    public static function setNumberRange(int $fromNumberRange, int $toNumberRange): self;

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
    public static function forModel(int $fromNumberRange, int $toNumberRange, string $tableName, string $tableColumnName): self;

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
    public static function forModelUsingDefaultConfigNumberRange(string $tableName, string $tableColumnName): self;

    /**
     * Generates a secure random number within the configured range.
     *
     * This method creates a cryptographically secure random number between the
     * specified minimum and maximum values. If table name and column are provided,
     * it also validates that the generated number is unique in the specified
     * database table column. If the generated number already exists in the database,
     * it recursively generates a new number until a unique one is found.
     *
     * @return int A secure random number within the configured range that is unique
     *             in the database if table validation is configured
     */
    public function generate(): int;

    /**
     * Generates multiple secure random numbers within the configured range.
     *
     * @param  int  $count  Number of unique random numbers to generate
     * @return array<int> Array of unique secure random numbers
     *
     * @throws RuntimeException If unable to generate enough unique numbers
     */
    public function generateBatch(int $count): array;

    /**
     * Generates a secure random number with optional prefix and suffix.
     *
     * @param  string  $prefix  String to prepend to the number
     * @param  string  $suffix  String to append to the number
     * @return string Formatted random number with prefix and suffix
     */
    public function generateFormatted(string $prefix = '', string $suffix = ''): string;

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

    /**
     * Sets the table and column for uniqueness validation.
     *
     * @param  string  $table  The database table name
     * @param  string  $column  The column name in the table
     */
    public function uniqueIn(string $table, string $column): self;

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

    /**
     * Sets the minimum value for the random number range.
     *
     * @param  int  $min  The minimum value (inclusive)
     */
    public function min(int $min): self;

    /**
     * Sets the maximum value for the random number range.
     *
     * @param  int  $max  The maximum value (inclusive)
     */
    public function max(int $max): self;
}
