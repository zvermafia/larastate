<?php

declare(strict_types=1);

namespace Zvermafia\Larastate\Abstracts;

use UnexpectedValueException;
use Exception;
use BadMethodCallException;
use ReflectionClass;
use Zvermafia\Larastate\Traits\LocaleKeysTrait;
use Zvermafia\Larastate\Traits\LocaleValuesTrait;
use Zvermafia\Larastate\Traits\LocalePathsTrait;
use Zvermafia\Larastate\Traits\HelpersTrait;

abstract class StateAbstract
{
    use LocaleValuesTrait;

    /**
     * A localizations file path for the entity.
     *
     * Optional.
     *
     * @var string
     */
    protected $locale_path;

    /** @var \ReflectionClass */
    protected $reflection;

    /**
     * Create a new instance.
     */
    public function __construct()
    {
        $this->reflection = new ReflectionClass(static::class);
    }

    /**
     * Magic method.
     *
     * Available methods (for example, role state with acceptable values are member and admin):
     *     getRoleValues() // returns all the acceptable values, example: ['member', 'admin']
     *     getRoleValuesWithLocales() // returns array, example: ['member' => 'Member', 'admin' => 'Administrator']
     *     getRoleLocale($role_value) // return string, example: 'Administrator'
     *
     * @param string $name
     * @param array $arguments
     * @throws \BadMethodCallException If the there is not a calling method
     * @throws \Exception If the constant not found
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $matches = [];

        // check method pattern
        if (! preg_match('/^(get)([a-zA-Z_\x80-\xff]+)(Values|ValuesWithLocales|Locale)$/', $name, $matches)) {
            throw new BadMethodCallException("The {$name}() method you are calling was not found.");
        }

        list($method_name, $prefix, $state_name, $postfix) = $matches;
        $state_name_snake_case = $this->makeSnakeCase($state_name);
        $state_constant_name = $this->makeConstantName($state_name);

        if ($this->reflection->hasConstant($state_constant_name) === false) {
            throw new Exception("The '{$state_constant_name}' constant not found.");
        }

        switch ($postfix) {
            case 'Values':
                return $this->getValues($state_name_snake_case);

            case 'ValuesWithLocales':
                return $this->getValuesWithLocales($state_name_snake_case);

            case 'Locale':
                return $this->getLocale($state_name_snake_case, $arguments[0]);
        }
    }

    /**
     * Get the values for the given state name.
     *
     * @param string $state_name
     * @return array
     */
    private function getValues(string $state_name): array
    {
        $constant_name = $this->makeConstantName($state_name);

        return array_values($this->reflection->getConstant($constant_name));
    }

    /**
     * Get values and locales for the given state.
     *
     * @param string $state_name
     * @throws \UnexpectedValueException If amount of the state values and their localizations are different
     * @throws \UnexpectedValueException If the state values and their localizations don't match by key
     * @return array
     */
    private function getValuesWithLocales(string $state_name): array
    {
        $values = array_flip($this->getValues($state_name)); // from a CONSTANT
        $values_with_locales = $this->getLocales($state_name); // from a localization file

        if (count($values) !== count($values_with_locales)) {
            throw new UnexpectedValueException("Amount of the state values and their localizations are different.");
        }

        if (count(array_diff_key($values, $values_with_locales))) {
            throw new UnexpectedValueException("The state values and their localizations don't match by key.");
        }

        return $values_with_locales;
    }
}
