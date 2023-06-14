<?php

namespace CreativeCrafts\LaravelSecureRandomNumberGenerator\Commands;

use Illuminate\Console\Command;

class LaravelSecureRandomNumberGeneratorCommand extends Command
{
    public $signature = 'laravel-secure-random-number-generator';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
