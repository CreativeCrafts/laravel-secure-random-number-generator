<?php

declare(strict_types=1);

return [
    /** This is the start of the random number range */
    'from_number_range' => 1000,
    /** This is the end of the random number range */
    'to_number_range' => 9999999,
    /** Prevent infinite recursion */
    'max_retries' => 100,
    /** Enable caching for the generated random numbers */
    'use_cache' => false,
    /** Cache time in seconds */
    'cache_time' => 60,
    /** Default pattern for generating random numbers */
    'default_pattern' => '#####',
    /** Maximum batch size for a single operation*/
    'max_batch_size' => 1000,
    /** Multiplier for candidate generation (higher means more candidates per batch) */
    'batch_candidate_multiplier' => 2,
];
