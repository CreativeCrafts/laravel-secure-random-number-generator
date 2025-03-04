<?php

declare(strict_types=1);

namespace CreativeCrafts\SecureRandomNumberGenerator\Traits;

use CreativeCrafts\SecureRandomNumberGenerator\SecureRandomNumberGenerator;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasSecureRandomNumber
{
    /**
     * Boot the HasSecureRandomNumber trait.
     *
     * This method is automatically called when the trait is used in a model.
     * It registers a creating event listener that automatically generates and
     * assigns a secure random number to the specified column if it's empty.
     */
    protected static function bootHasSecureRandomNumber(): void
    {
        static::creating(function ($model) {
            // Check if the column is already set
            $column = $model->getRandomNumberColumn();

            if (empty($model->{$column})) {
                $model->{$column} = $model::generateRandomNumber();
            }
        });
    }

    /**
     * Get the column that should receive the random number.
     *
     * This method returns the name of the database column that will store the
     * generated secure random number. It checks if a custom column name has been
     * defined in the model via the $randomNumberColumn property, otherwise it
     * defaults to 'reference_number'.
     *
     * @return string The name of the column that will store the random number
     */
    public function getRandomNumberColumn(): string
    {
        return $this->randomNumberColumn ?? 'reference_number';
    }

    /**
     * Generate a secure random number for this model.
     *
     * This method creates a new instance of the model to access its table name
     * and random number column, then uses the SecureRandomNumberGenerator to
     * generate a unique random number within the default configuration range.
     *
     * @return int The generated secure random number that is unique within the model's table
     */
    public static function generateRandomNumber(): int
    {
        $model = new static();
        $table = $model->getTable();
        $column = $model->getRandomNumberColumn();

        return SecureRandomNumberGenerator::forModelUsingDefaultConfigNumberRange($table, $column)
            ->generate();
    }
}
