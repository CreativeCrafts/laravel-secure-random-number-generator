# Changelog

All notable changes to `laravel-secure-random-number-generator` will be documented in this file.

## 0.0.1 - 2023-06-14

- initial release

## 0.0.2 - 2023-06-20

- added configuration file and split the functionality into modules

## 0.0.3 - 2023-06-20

- added command to publish the configuration file in the README file

## 0.0.4 - 2023-06-20

- updated composer packages

## 0.0.5 - 2023-06-20

- added a feature to generate a random unique number for a model with a default value set in the configuration file

## 0.0.6 - 2023-06-20

- added declare strict types using pint

## 1.0.0 - 2024-03-17

- Added support for Laravel 11

## 1.1.0 - 2025-03-03

- Added support for Laravel 12
- Added these new methods:
  - `generateBatch`
  - `generateFormatted`
  - `generateWithPattern`
  - `uniqueIn`
  - `generateBatchFormatted`
  - `generateBatchWithPattern`
  - `min`
  - `max`
- Added new trait
