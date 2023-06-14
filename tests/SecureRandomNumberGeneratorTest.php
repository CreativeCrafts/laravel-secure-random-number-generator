<?php

use CreativeCrafts\SecureRandomNumberGenerator\SecureRandomNumberGenerator;

it('can generate a secure random number', function () {
    $generateNumber = SecureRandomNumberGenerator::setConfig(10001, 9999999, '', '')->generate();
    expect($generateNumber)->toBeInt();
})->group('SecureRandomNumberGenerator');
