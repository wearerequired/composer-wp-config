<?php
/**
 * Configuration for WordPress.
 *
 * THIS FILE IS AUTO-GENERATED. DO NOT EDIT THIS FILE.
 */

/**
 * Adjust HTTPS and IP detection.
 */
if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
	$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}

/**
 * Require Composer's autoloader.
 */
require_once __DIR__ . '/{{{VENDOR_DIR}}}/autoload.php';

global $table_prefix;

/**
 * Environment variables.
 *
 * - Load from `../shared/.env`, `../configs/.env`, `../.env`, or `./.env`.
 * - Check for required variables.
 * - Define constant for each variable if not already defined.
 */
\Env::init(); // Exposes the function env().

$root      = dirname( __DIR__ );
$dotenv    = \Dotenv\Dotenv::createImmutable( [ $root . '/shared', $root . '/configs', $root, __DIR__ ] );
$variables = $dotenv->load();
$dotenv->required(
	[
		'_HTTP_HOST',
		'DB_NAME',
		'DB_USER',
		'DB_PASSWORD',
		'AUTH_KEY',
		'SECURE_AUTH_KEY',
		'LOGGED_IN_KEY',
		'NONCE_KEY',
		'AUTH_SALT',
		'SECURE_AUTH_SALT',
		'LOGGED_IN_SALT',
		'NONCE_SALT',
	]
)->notEmpty();

$variable_names = array_keys( $variables );
array_walk(
	$variable_names,
	function ( $name ) {
		switch ( $name ) {
			// Some variables are not used as a constant.
			case '_HTTP_HOST':
			case 'URL_DEVELOPMENT':
			case 'URL_STAGING':
			case 'URL_PRODUCTION':
				break;

			// Assign table prefix to the $GLOBALS variable.
			case 'DB_TABLE_PREFIX':
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Initial assignment.
				$GLOBALS['table_prefix'] = env( $name );
				break;

			// Any other variable will be a constant unless it's already defined.
			default:
				if ( ! defined( $name ) ) {
					$value = env( $name );
					if ( is_string( $value ) ) {
						$value = str_replace( '{{DIR}}', __DIR__, $value );
					}
					define( $name, $value );
				}
				break;
		}
	}
);
unset( $root, $dotenv, $variables, $variable_names );

/**
 * Environment settings.
 */
$envs = [
	'development' => env( 'URL_DEVELOPMENT' ),
	'staging'     => env( 'URL_STAGING' ),
	'production'  => env( 'URL_PRODUCTION' ),
];
define( 'ENVIRONMENTS', $envs );
defined( 'WP_ENV' ) || define( 'WP_ENV', 'development' );

/**
 * Debugging settings.
 */
defined( 'QM_DISABLED' ) || define( 'QM_DISABLED', true );
defined( 'SAVEQUERIES' ) || define( 'SAVEQUERIES', false );
defined( 'WP_DEBUG' ) || define( 'WP_DEBUG', false );
defined( 'WP_DISABLE_FATAL_ERROR_HANDLER' ) || define( 'WP_DISABLE_FATAL_ERROR_HANDLER', WP_DEBUG );
defined( 'WP_DEBUG_LOG' ) || define( 'WP_DEBUG_LOG', false );
defined( 'WP_DEBUG_DISPLAY' ) || define( 'WP_DEBUG_DISPLAY', false );
defined( 'SCRIPT_DEBUG' ) || define( 'SCRIPT_DEBUG', false );
defined( 'DISALLOW_FILE_MODS' ) || define( 'DISALLOW_FILE_MODS', true );

/**
 * Database settings.
 */
defined( 'DB_HOST' ) || define( 'DB_HOST', 'localhost' );
defined( 'DB_CHARSET' ) || define( 'DB_CHARSET', 'utf8' );
defined( 'DB_COLLATE' ) || define( 'DB_COLLATE', '' );

if ( empty( $table_prefix ) ) {
	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Initial assignment.
	$table_prefix = 'wp_';
}

/**
 * Object caching.
 */
defined( 'WP_CACHE_KEY_SALT' ) || define( 'WP_CACHE_KEY_SALT', WP_ENV );

/**
 * URLs and paths.
 */
if ( ! defined( 'WP_HOME' ) ) {
	$server = filter_input_array(
		INPUT_SERVER,
		[
			'HTTPS'       => FILTER_SANITIZE_STRING,
			'SERVER_NAME' => FILTER_SANITIZE_URL,
		]
	);
	$secure = in_array( (string) $server['HTTPS'], [ 'on', '1' ], true );
	$scheme = $secure ? 'https://' : 'http://';
	$name   = $server['SERVER_NAME'] ?: env( '_HTTP_HOST' );
	define( 'WP_HOME', $scheme . $name );

	unset( $server, $secure, $scheme, $name );
}

defined( 'WP_SITEURL' ) || define( 'WP_SITEURL', WP_HOME );

defined( 'WP_CONTENT_DIR' ) || define( 'WP_CONTENT_DIR', __DIR__ . '/content' );
defined( 'WP_CONTENT_URL' ) || define( 'WP_CONTENT_URL', WP_HOME . '/content' );

/**
 * Sets up WordPress vars and included files.
 */
require_once ABSPATH . 'wp-settings.php';
