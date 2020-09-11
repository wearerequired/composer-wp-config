# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
* Define `WP_ENVIRONMENT_TYPE` with the value of `WP_ENV` for WordPress 5.5 support.

### Changed
* Update PHP dotenv dependency to v5.1.
* Update env dependency to v2.1.

## [0.3.1] - 2020-05-12

### Fixed
* Fix unintended type conversion to string on boolean and integer values due to `{{DIR}}` replacement.

## [0.3.0] - 2020-04-03

### Changed
* Don't serialize array value for `ENVIRONMENTS` constant.
* Add PHPCS config and fix remaining coding standard issues.

## [0.2.0] - 2020-02-21

### Changed
* Update PHP dotenv dependency to v4.

## [0.1.0] - 2020-02-21

### Added
* Create `wp-config.php` one level above the WordPress installation.
* Include `require_once` call for Composer's `autoload.php`.

[Unreleased]: https://github.com/wearerequired/composer-wp-config/compare/0.3.1...HEAD
[0.3.1]: https://github.com/wearerequired/composer-wp-config/compare/0.3.0...0.3.1
[0.3.0]: https://github.com/wearerequired/composer-wp-config/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/wearerequired/composer-wp-config/compare/0.1.0...0.2.0
[0.1.0]: https://github.com/wearerequired/composer-wp-config/compare/7a01662...0.1.0
