# composer-wp-config

A composer plugin to create the WordPress configuration file which automagically defines constants from a .env file. Also includes path of Composer's autoloader in wp-config.php.

## Installation

Via Composer

```
composer require johnpbloch/wordpress-core wearerequired/composer-wp-config
```

Copy [`.env.example`](res/.env.example) and save it as `.env`. The variables are searched relative to `wp-config.php` in `../shared/.env`, `../configs/env`, `../.env`, or `./.env`.

### List of required variables

* `_HTTP_HOST` (Used when `$_SERVER['SERVER_NAME']` is not set, like WP-CLI.)
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

See also the list of [default constants](#default-constants).

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


## Default Constants

If the following variables are not defined they will be assigned a default value:

| Variable | Default Value |
|--------------------|------------------------------------|
| `WP_ENV` | `'development'` |
| `QM_DISABLED` | `true` |
| `SAVEQUERIES` | `false` |
| `WP_DEBUG` | `false` |
| `WP_DEBUG_LOG` | `false` |
| `WP_DEBUG_DISPLAY` | `false` |
| `SCRIPT_DEBUG` | `false` |
| `DISALLOW_FILE_MODS` | `false` |
| `DB_HOST` | `'localhost'` |
| `DB_CHARSET` | `'utf8'` |
| `DB_COLLATE` | `''` |
| `$table_prefix` | `'wp_'` |
| `WP_CACHE_KEY_SALT` | Value of `WP_ENV` |
| `WP_HOME` | Based on `$_SERVER['SERVER_NAME']` |
| `WP_SITEURL` |  Value of `WP_SITEURL` |
| `WP_CONTENT_DIR` | `__DIR__ . '/content'` |
| `WP_CONTENT_URL` | `WP_HOME . '/content'` |