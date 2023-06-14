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

    public static function setConfig(int $fromNumberRange, int $toNumberRange, string $tableName, string $tableColumnName): self
    {
        return new self($fromNumberRange, $toNumberRange, $tableName, $tableColumnName);
    }

    public function generate(): int
    {
        $randomizer = new Randomizer(new Secure());
        $generatedNumber = $randomizer->getInt($this->fromNumberRange, $this->toNumberRange);

        if ($this->tableName === '' || $this->tableColumnName === '') {
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
}
