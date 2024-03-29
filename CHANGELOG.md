# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-03-26

### Added
* Introduce `required_wp_config.autoloader_loaded` and `required_wp_config.pre_load_wp_settings` hooks.

## [0.9.0] - 2024-03-20

### Changed
* Allow the WordPress plugin API to be used by vendor modules.

## [0.8.0] - 2023-04-20

### Added
* Add support for `WP_CONTENT_FOLDER_NAME` variable to customize the default value `content`.

### Fixed
* Avoid deprecation notice for use of deprecated `FILTER_SANITIZE_STRING` constant in PHP 8.1.

## [0.7.1] - 2022-12-07

### Fixed
* Prevent "Constant ENVIRONMENTS already defined" warnings if `ENVIRONMENTS` is already defined.

## [0.7.0] - 2021-10-22

### Fixed
* Fix empty constants by falling back to `Dotenv::createArrayBacked()` if `Dotenv::createUnsafeImmutable()` returns an empty array.

### Changed
* Require Composer's autoloader before `wp-config-prepend.php` allowing for use of all available libraries.

## [0.6.0] - 2021-07-01

### Added
* Add support for automatically loading a file before (`wp-config-prepend.php`) and after (`wp-config-append.php`) the default config.
* Load WordPress plugin API early so actions and filters can be used in `wp-config.php`, `wp-config-prepend.php`, and `wp-config-append.php`.

## [0.5.1] - 2021-06-11

### Fixed
* Fix fallback detection for `WP_HOME` if entries in the superglobal `$_SERVER` are set manually, like `$_SERVER['HTTPS']`.

## [0.5.0] - 2021-05-28

### Changed
* Require PHP 7.4.
* Require Composer v2.

## [0.4.0] - 2020-12-01

### Added
* Define `WP_ENVIRONMENT_TYPE` with the value of `WP_ENV` for WordPress 5.5 support.
* Support Composer 2.0.
* Delete `wp-config.php` after WordPress package is removed.
* Add `composer build-wp-config` command to rebuild `wp-config.php` file.

### Changed
* Change default path for `.env` file to only search next to `wp-config.php`. Use `wp-config-env-paths` to change the path(s).
* Update PHP dotenv dependency from v4.1 to v5.1.
* Update env dependency from v1.1 to v2.1.

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

[Unreleased]: https://github.com/wearerequired/composer-wp-config/compare/1.0.0...HEAD
[0.9.0]: https://github.com/wearerequired/composer-wp-config/compare/0.9.0...1.0.0
[0.9.0]: https://github.com/wearerequired/composer-wp-config/compare/0.8.0...0.9.0
[0.8.0]: https://github.com/wearerequired/composer-wp-config/compare/0.7.1...0.8.0
[0.7.1]: https://github.com/wearerequired/composer-wp-config/compare/0.7.0...0.7.1
[0.7.0]: https://github.com/wearerequired/composer-wp-config/compare/0.6.0...0.7.0
[0.6.0]: https://github.com/wearerequired/composer-wp-config/compare/0.5.0...0.6.0
[0.5.1]: https://github.com/wearerequired/composer-wp-config/compare/0.5.0...0.5.1
[0.5.0]: https://github.com/wearerequired/composer-wp-config/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/wearerequired/composer-wp-config/compare/0.3.1...0.4.0
[0.3.1]: https://github.com/wearerequired/composer-wp-config/compare/0.3.0...0.3.1
[0.3.0]: https://github.com/wearerequired/composer-wp-config/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/wearerequired/composer-wp-config/compare/0.1.0...0.2.0
[0.1.0]: https://github.com/wearerequired/composer-wp-config/compare/7a01662...0.1.0
