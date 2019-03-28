# Composer Plugin to inject wp-config.php

## Installation

Via Composer

```
composer require johnpbloch/wordpress-core wearerequired/composer-wp-config
```

Create an `.env` file. The variables are searched in `../shared/.env`, `../configs/env`, `../.env`, or `./.env`.

List of required variables:

* `_HTTP_HOST` (used for CLI host)
* `DB_NAME`
* `DB_USER`
* `DB_PASSWORD`
* `AUTH_KEY`
* `SECURE_AUTH_KEY`
* `LOGGED_IN_KEY`
* `NONCE_KEY`
* `AUTH_SALT`
* `SECURE_AUTH_SALT`
* `LOGGED_IN_SALT`
* `NONCE_SALT`

## Features
* Creates `wp-config.php` one level above the WordPress installation.
* The `wp-config.php` includes the path to Composer's autoloader.
* Searches for `.env` file with the help of [PHP dotenv](https://github.com/vlucas/phpdotenv).
* Defines all variables as constants unless a constant is already set.
* Defines reasonable default values for database, object cache, debug, URL, and path constants.
* Use `{{DIR}}` as placeholder in variable values to get it replaced with `__DIR__` of `wp-config.php`.

## Planned Features

* Make path for `.env` file customizable via `composer.json`.
* Allow to change required variables via `composer.json`.
* Allow to change variables not used as a constant via `composer.json`.
* Let us know what you think is missing…
