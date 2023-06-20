<?php

declare(strict_types=1);

use CreativeCrafts\SecureRandomNumberGenerator\SecureRandomNumberGenerator;

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
