<?php

declare(strict_types=1);

namespace Zvermafia\Larastate\Traits;

trait LocaleKeysTrait
{
    use LocalePathsTrait;

    /**
     * Get a locale key built by the given state's name and value.
     *
     * @param string $state_name
     * @param mixed $state_value
     * @return string
     */
    protected function getLocaleKey(string $state_name, $state_value): string
    {
        return "{$this->getLocalePath()}.state.{$state_name}.{$state_value}";
    }

    /**
     * Get a locales key built by the given state's name.
     *
     * @param string $state_name
     * @return string
     */
    protected function getLocalesKey(string $state_name): string
    {
        return "{$this->getLocalePath()}.state.{$state_name}";
    }
}
