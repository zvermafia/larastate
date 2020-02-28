<?php

declare(strict_types=1);

namespace Zvermafia\Larastate\Traits;

trait LocaleValuesTrait
{
    use LocaleKeysTrait;

    /**
     * Get a localization for the given state and its value.
     *
     * @param string $state_name
     * @param mixed $state_value
     * @return string
     */
    private function getLocale(string $state_name, $state_value): string
    {
        return trans($this->getLocaleKey($state_name, $state_value));
    }

    /**
     * Get a localizations for the given state.
     *
     * @param string $state_name
     * @return array
     */
    private function getLocales(string $state_name): array
    {
        return trans($this->getLocalesKey($state_name));
    }
}
