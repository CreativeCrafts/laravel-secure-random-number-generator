<?php

namespace CreativeCrafts\SecureRandomNumberGenerator;

use Illuminate\Support\Facades\Validator;
use Random\Engine\Secure;
use Random\Randomizer;

class SecureRandomNumberGenerator
{
    public function __construct(protected int $fromNumberRange, protected int $toNumberRange, protected string $tableName = '', protected string $tableColumnName = '')
    {
    }

    public static function useDefaultConfigNumberRange(): self
    {
        return new self(self::fromRangeNumber(), self::toRangeNumber(), '', '');
    }

    public static function setNumberRange(int $fromNumberRange, int $toNumberRange): self
    {
        return new self($fromNumberRange, $toNumberRange, '', '');
    }

    public static function forModel(int $fromNumberRange, int $toNumberRange, string $tableName, string $tableColumnName): self
    {
        return new self($fromNumberRange, $toNumberRange, $tableName, $tableColumnName);
    }

    public function generate(): int
    {
        $randomizer = new Randomizer(new Secure());

        $generatedNumber = $randomizer->getInt($this->fromNumberRange, $this->toNumberRange);

        if (! isset($this->tableColumnName, $this->tableName) || $this->tableName === '' || $this->tableColumnName === '') {
            return $generatedNumber;
        }

        $numberGenerator = [
            $this->tableColumnName => $generatedNumber,
        ];

        $uniqueValidationRule = [
            $this->tableColumnName => 'unique:'.$this->tableName.','.$this->tableColumnName,
        ];
        $validator = Validator::make($numberGenerator, $uniqueValidationRule)->passes();

        return $validator ? $numberGenerator[$this->tableColumnName] : self::generate();
    }

    protected static function fromRangeNumber(): int
    {
        /** @var int $fromNumberRange */
        $fromNumberRange = config('secure-random-number-generator.from_number_range');

        return $fromNumberRange;
    }

    protected static function toRangeNumber(): int
    {
        /** @var int $toNumberRange */
        $toNumberRange = config('secure-random-number-generator.to_number_range');

        return $toNumberRange;
    }
}
