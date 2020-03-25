# Larastate — a main source for your entity states values

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Easy access to entity states values and to their localizations.

### Navigation by sections
- <a href="#introduction">Introduction</a>
    - <a href="#the-problem">The problem</a>
    - <a href="#a-solution">A solution</a>
- <a href="#install">Install</a>
- <a href="#setup">Setup</a>
- <a href="#usage">Usage</a>
- <a href="#alternatives">Alternatives</a>
- <a href="#change-log">Change log</a>
- <a href="#contributing">Contributing</a>
- <a href="#security">Security</a>
- <a href="#credits">Credits</a>
- <a href="#license">License</a>

## Introduction

### The problem

When you have an entity with some states you usually need to access to those states' values and to their localizations. Let's consider the following cases:

*Let's assume we have a User entity with a role state. And the role state can only accept 'member', 'moderator' and 'administrator' values.*

* **When you need to validate a state's acceptable values.** So when we try to create a new user we need to validate the role state value. But how to do it if we don't have a main source for that values? And usually we may do like below:
    ```php
        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {
            return [
                // ... other validation rules
                'role' => [
                    'required',
                    Rule::in(['member', 'moderator', 'administrator']), // 🙁 values are hardcoded
                ],
            ];
        }
    ```
* **When you need to display localization for one of the state's values.** For example when you need to give an information about a user's role:
    ```php
        <li>{{ $user->name }}</li>
        <li>{{ $user->email }}</li>
        <li>{{ trans("user.states.role.{$user->role}") }}</li>
    ```
    🙁 Localization file path, file name and locale key are hardcoded.
    
* **When you need to display those values with locales.** For example in a select box:
    ```php
        @foreach (['member', 'moderator', 'administrator'] as $value)
            <option value="{{ $value }}">@lang("user.states.role.{$value}")</option>
        @endforeach
    ```
    🙁 Hardcoded, hardcoded, ...

And yes when you hardcoded those values in different places of your project then it will be harder to change (add, remove, rename) those values. Because you don't use a main source for those values.

### A solution
Unfortunately Laravel doesn't provide a solution for this problem out of the box. So I've created this package. With this package you will not have problems in the above section! Let's solve those problems in the appropriate order.

Firstly create a `States` folder in the `app` directory, then create a `UserState` class in it with extending a `StateAbstract` abstract class. And define a `ROLE` constant in the class with those values from the above section. You should have something below:

```php
<?php // app/States/UserState.php

namespace App\States\UserState;

use Zvermafia\Larastate\Abstracts\StateAbstract;

class UserState extends StateAbstract
{
    /** @var array */
    public const ROLE = [
        'member' => 'member',
        'moderator' => 'moderator',
        'administrator' => 'administrator',
    ];
}
```

* **When you need to validate a state's acceptable values:**
    ```php
        /**
         * Get the validation rules that apply to the request.
         *
         * @param \App\States\UserState $user_states
         * @return array
         */
        public function rules(UserState $user_states)
        {
            return [
                // ... other validation rules
                'role' => [
                    'required',
                    Rule::in($user_states->getRoleValues()), // 🙂 values are not hardcoded!
                ],
            ];
        }
    ```

* **When you need to display localization for one of the state's values.** For example when you need to give an information about a user's role:
    ```php
        <li>{{ $user->name }}</li>
        <li>{{ $user->email }}</li>
        <li>{{ $user_states->getRoleLocale($user->role) }}</li>
    ```
    🙂 Localization file path, file name and locale key are taken from the single source!
    
* **When you need to display those values with locales.** For example in a select box:
    ```php
        @foreach ($user_states->getRoleValuesWithLocales() as $value => $locale)
            <option value="{{ $value }}">{{ $locale }}</option>
        @endforeach
    ```
    🙂 Much better!

## Install

To install the package just pull it via composer and you're ready to setup.

``` bash
$ composer require zvermafia/larastate
```

## Setup

The package have some convention about structuring a state classes directory, naming those classes and naming constants. But all this can be changed to suit your needs by extending a `StateAbstract` abstract class. So if you need it then take a look at the source code.

* By default you should put all your state classes to `app/States` directory. And yes usually there is not that directory so you need to create it by yourself.
* And your state class names should end with `State` postfix, for example `UserState`, `PageState`, `OrderState` and so on...
* Constant names should be in upper case and each word separated by underscore.

Let's assume we want to setup a state class for a post entity, so our steps are follows:
1. Create a `States` folder in the `app` directory;
2. Create a `PostState.php` file and put it into the `app/States` directory;
3. Extend a `StateAbstract` abstract class and define constants, you should have something below:
    ```php
    <?php
    
    namespace App\States;
    
    use Zvermafia\Larastate\Abstracts\StateAbstract;

    class PostState extends StateAbstract
    {
        /** @var array */
        public const STATUS = [
            'draft' => 0,
            'published' => 1,
        ];
        
        /** @var array */
        public const TYPE = [
            'info' => 0,
            'blog' => 1,
            'news' => 2,
        ];
    }
    ```
4. Create localization file in a `resources/lang/en/entities` directory with the entity name, so in our case it will be `post.php` . It's a regular Laravel's localization file but by default all state localizations should be grouped in a `state` key of the array. So you should have something like below:
    ```php
    <?php // resources/lang/en/entities/post.php

    return [
        // here's other localization like attribute/property...
        
        'state' => [
            'status' => [
                App\States\PostState::STATUS['draft'] => 'Draft',
                App\States\PostState::STATUS['published'] => 'Published',
            ],
            'type' => [
                App\States\PostState::TYPE['info'] => 'Static page',
                App\States\PostState::TYPE['blog'] => 'Blog post',
                App\States\PostState::TYPE['news'] => 'News post',
            ],
        ],
    ];
    ```

## Usage

For usage example we will use our `PostState` which we created it in the above section.

There are three cases and all works through PHP's magic `__call()` method:
1. Getting values for the state
2. Getting values with locales for the state
3. Getting a locale for one of the state's values

All methods start with prefix `get` and ends with one of the case names (`Values`, `ValuesWithLocales` or `Locale`).
And between those prefix and postfixes you should write your state name in `StudlyCase` format.

For a `type` state those will be:
1. `getTypeValues()`
2. `getTypeValuesWithLocales()`
3. `getTypeLocale(1)`

``` php
$post_states = new App\States\PostState();

$post_states->getTypeValues(); // [0, 1, 2]
$post_states->getTypeValuesWithLocales(); // [0 => 'Static page', 1 => 'Blog post', 2 => 'News post']
$post_states->getTypeValues(); // 'Blog post'
```

## Alternatives

- [BenSampo/laravel-enum](https://github.com/BenSampo/laravel-enum)
- [artkonekt/enum-eloquent](https://github.com/artkonekt/enum-eloquent)
- [nasyrov/laravel-enums](https://github.com/nasyrov/laravel-enums)
- [mad-web/laravel-enum](https://github.com/mad-web/laravel-enum)
- [spatie/laravel-enum](https://github.com/spatie/laravel-enum)
- [cerbero90/laravel-enum](https://github.com/cerbero90/laravel-enum)

So why I've created this package when there is already such kind of packages?
*Because they are a bit complicated for my simple task.*

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CODE_OF_CONDUCT](.github/CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email mohirjon@gmail.com instead of using the issue tracker.

## Credits

- [Mokhirjon Naimov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/zvermafia/larastate.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/zvermafia/larastate/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/zvermafia/larastate.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/zvermafia/larastate.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/zvermafia/larastate.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/zvermafia/larastate
[link-travis]: https://travis-ci.org/zvermafia/larastate
[link-scrutinizer]: https://scrutinizer-ci.com/g/zvermafia/larastate/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/zvermafia/larastate
[link-downloads]: https://packagist.org/packages/zvermafia/larastate
[link-author]: https://github.com/zvermafia
[link-contributors]: ../../contributors
