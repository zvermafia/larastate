<?php

declare(strict_types=1);

namespace Zvermafia\Larastate\Traits;

trait LocalePathsTrait
{
    use HelpersTrait;

    /**
     * Get a locales path for an entity.
     *
     * @return string
     */
    protected function getLocalePath(): string
    {
        if ($this->reflection->hasProperty('locale_path') && is_string($this->locale_path)) {
            return $this->locale_path;
        }

        return $this->generateLocalePath();
    }

    /**
     * Generate a locales path by the current class name.
     *
     * @throws \Exception
     * @return string
     */
    protected function generateLocalePath(): string
    {
        $pattern = '/(?<entity_name>^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)(?<postfix>State)$/';
        $matches = [];

        // if the class name matches the state class rule then the $matches[1] is value will be the entity name
        // else throw an exception
        if (! preg_match($pattern, $this->reflection->getShortName(), $matches)) {
            throw new Exception("The {$this->reflection->getShortName()} doesn't match the state class name rule.");
        }

        $entity_name = $this->makeSnakeCase($matches['entity_name']);

        return "entities/{$entity_name}";
    }
}
