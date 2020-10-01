# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Add
- Add a function with the name `larastate` to use it like facade for all the state classes.
  The function accepts a state name as the parameter and resolves a class by the given state name then returns it.
  For example `larastate('user')` must return `App\States\UserState` so you can simply get `role` state values
  of the `User` entity with `larastate('user')->getRoleValues();`.
  So you don't need to instantiate state class in controllers then pass it to views,
  in views you can use `larastate()` global function.

## [0.1.3] - 2020-10-01
### Added
- Supporting Laravel ^8.0

### Changed
- Updated some information about the package in the composer.json file
- Fixes coding standart name in the phpcs.xml.dist file

### Removed
- Supporting Laravel ^5.6

## [0.1.2] - 2020-03-25
## [0.1.1] - 2020-03-02
## [0.1.0] - 2020-02-28

[Unreleased]: https://github.com/zvermafia/larastate/compare/v0.1.2...HEAD
[0.1.2]: https://github.com/zvermafia/larastate/compare/v0.1.1...v0.1.2
[0.1.1]: https://github.com/zvermafia/larastate/compare/v0.1.0...v0.1.1
[0.1.0]: https://github.com/zvermafia/larastate/releases/tag/v0.1.0
