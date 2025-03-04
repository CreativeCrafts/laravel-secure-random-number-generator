<?php

declare(strict_types=1);

use CreativeCrafts\SecureRandomNumberGenerator\SecureRandomNumberGenerator;
use Illuminate\Support\Facades\DB;

it('can generate a secure random number', function () {
    $generateNumber = SecureRandomNumberGenerator::setNumberRange(10001, 9999999)->generate();
    expect($generateNumber)->toBeInt();
})->group('SecureRandomNumberGenerator');

it('can generate a secure random number with default config number range', function () {
    $randomNumber = SecureRandomNumberGenerator::useDefaultConfigNumberRange()->generate();
    expect($randomNumber)->toBeInt();
});

it('can generate a secure random unique number for model', function () {
    $uniqueRandomNumber = SecureRandomNumberGenerator::forModel(1, 100, 'users', 'registration_number')->generate();
    expect($uniqueRandomNumber)->toBeInt();
})->skip('This test will fail because the table users does not exist in the database.');

it('can generate a secure random unique number for model using default configuration', function () {
    $uniqueRandomNumber = SecureRandomNumberGenerator::forModelUsingDefaultConfigNumberRange('users', 'registration_number')->generate();
    expect($uniqueRandomNumber)->toBeInt();
})->skip('This test will fail because the table users does not exist in the database.');

describe('SecureRandomNumberGenerator::generateBatch()', function () {
    it('returns empty array when count is zero or negative', function () {
        $generator = SecureRandomNumberGenerator::useDefaultConfigNumberRange();

        expect($generator->generateBatch(0))->toBeArray()->toBeEmpty()
            ->and($generator->generateBatch(-1))->toBeArray()->toBeEmpty();
    });

    it('generates the exact number of requested random numbers', function () {
        $generator = SecureRandomNumberGenerator::setNumberRange(1000, 9999);
        $count = 5;

        $result = $generator->generateBatch($count);
        expect($result)->toBeArray()
            ->toHaveCount($count)
            ->each->toBeInt();
    });

    it('generates unique numbers within the batch', function () {
        $generator = SecureRandomNumberGenerator::setNumberRange(1000, 9999);
        $count = 10;

        $result = $generator->generateBatch($count);

        expect(count(array_unique($result)))->toBe($count);
    });

    it('respects the configured number range', function () {
        $min = 5000;
        $max = 6000;
        $generator = SecureRandomNumberGenerator::setNumberRange($min, $max);

        $result = $generator->generateBatch(20);
        expect($result)->toBeArray();

        foreach ($result as $number) {
            expect($number)
                ->toBeGreaterThanOrEqual($min)
                ->toBeLessThanOrEqual($max);
        }
    });

    it('validates uniqueness against the database', function () {
        $tableName = 'test_table';
        $columnName = 'random_number';
        $existingNumbers = [1001, 1002, 1003];

        DB::shouldReceive('table')
            ->with($tableName)
            ->andReturnSelf();

        DB::shouldReceive('whereIn')
            ->with($columnName, Mockery::type('array'))
            ->andReturnSelf();

        DB::shouldReceive('pluck')
            ->with($columnName)
            ->andReturn(collect($existingNumbers));

        $generator = SecureRandomNumberGenerator::forModel(1000, 1010, $tableName, $columnName);
        $result = $generator->generateBatch(3);

        expect($result)->toHaveCount(3)
            ->and($result)->not->toContain($existingNumbers);
    });
});
