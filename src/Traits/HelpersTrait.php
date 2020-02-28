<?php

declare(strict_types=1);

namespace Zvermafia\Larastate\Traits;

use Illuminate\Support\Str;

trait HelpersTrait
{
    /**
     * Make snake_case from the given camelCase or StudlyCase.
     *
     * @param string $value
     * @return string
     */
    private function makeSnakeCase(string $value): string
    {
        return Str::snake($value);
    }

    /**
     * Make constant name from the given state name in StudlyCase.
     *
     * @param string $state_name
     * @return string
     */
    private function makeConstantName(string $state_name): string
    {
        return strtoupper(Str::snake($state_name));
    }
}
