<?php
/**
 * Your base production configuration goes in this file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 */

use Roots\WPConfig\Config;
use function Env\env;

// USE_ENV_ARRAY + CONVERT_* + STRIP_QUOTES.
Env\Env::$options
	= Env\Env::USE_ENV_ARRAY
	| Env\Env::CONVERT_BOOL
	| Env\Env::CONVERT_NULL
	| Env\Env::CONVERT_INT
	| Env\Env::STRIP_QUOTES;

/**
 * Directory containing all of the site's files
 *
 * @var string
 */
$root_dir = dirname( __DIR__ );

/**
 * Document Root
 *
 * @var string
 */
$webroot_dir = $root_dir . '/web';

/**
 * Use Dotenv to set required environment variables and load .env file in root
 * .env.local will override .env if it exists
 */
$env_files = file_exists( $root_dir . '/.env.local' )
	? [ '.env', '.env.pantheon', '.env.local' ]
	: [ '.env', '.env.pantheon' ];

$dotenv = Dotenv\Dotenv::createImmutable( $root_dir, $env_files, false );
if (
	// Check if a .env file exists.
	file_exists( $root_dir . '/.env' ) ||
	// Also check if we're using Lando and a .env.local file exists.
	( file_exists( $root_dir . '/.env.local' ) && isset( $_ENV['LANDO'] ) && 'ON' === $_ENV['LANDO'] )
) {
	$dotenv->load();
	if ( ! env( 'DATABASE_URL' ) ) {
		$dotenv->required( [ 'DB_NAME', 'DB_USER', 'DB_PASSWORD' ] );
	}
}

/**
 * Include Pantheon application settings.
 */
require_once __DIR__ . '/application.pantheon.php';

/**
 * Threespot: map Pantheon's environment to a logical WP_ENV, which picks the
 * config/environments/{development,staging,production}.php file below.
 *   live -> production, test -> staging,
 *   anything else (dev, multidev, Lando) -> development.
 * An explicit WP_ENV (e.g. from .env) wins. Off Pantheon with no WP_ENV,
 * fall back to production, the upstream default.
 */
$threespot_pantheon_env = $_ENV['PANTHEON_ENVIRONMENT'] ?? null;
define(
	'WP_ENV',
	env( 'WP_ENV' ) ?: match ( true ) {
		'live' === $threespot_pantheon_env => 'production',
		'test' === $threespot_pantheon_env => 'staging',
		null !== $threespot_pantheon_env => 'development',
		default => 'production',
	}
);
unset( $threespot_pantheon_env );

/**
 * Set WP_ENVIRONMENT_TYPE if not already defined
 */
if ( ! defined( 'WP_ENVIRONMENT_TYPE' ) ) {
	$wp_environment_type = env( 'WP_ENVIRONMENT_TYPE' );

	if ( $wp_environment_type ) {
		Config::define( 'WP_ENVIRONMENT_TYPE', $wp_environment_type );
	} elseif ( in_array( WP_ENV, [ 'production', 'staging', 'development', 'local' ], true ) ) {
		Config::define( 'WP_ENVIRONMENT_TYPE', WP_ENV );
	}
}

/**
 * Set WP_DEVELOPMENT_MODE if explicitly configured
 */
if ( ! defined( 'WP_DEVELOPMENT_MODE' ) ) {
	$wp_development_mode = env( 'WP_DEVELOPMENT_MODE' );

	if ( $wp_development_mode ) {
		Config::define( 'WP_DEVELOPMENT_MODE', $wp_development_mode );
	}
}

/**
 * DB settings
 */
if ( env( 'DB_SSL' ) ) {
	Config::define( 'MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL );
}

Config::define( 'DB_NAME', env( 'DB_NAME' ) );
Config::define( 'DB_USER', env( 'DB_USER' ) );
Config::define( 'DB_PASSWORD', env( 'DB_PASSWORD' ) );
Config::define( 'DB_HOST', env( 'DB_HOST' ) ?: 'localhost' );
Config::define( 'DB_CHARSET', 'utf8mb4' );
Config::define( 'DB_COLLATE', '' );
$table_prefix = env( 'DB_PREFIX' ) ?: 'wp_';

if ( env( 'DATABASE_URL' ) ) {
	$dsn = (object) parse_url( env( 'DATABASE_URL' ) );

	Config::define( 'DB_NAME', substr( $dsn->path, 1 ) );
	Config::define( 'DB_USER', $dsn->user );
	Config::define( 'DB_PASSWORD', isset( $dsn->pass ) ? $dsn->pass : null );
	Config::define( 'DB_HOST', isset( $dsn->port ) ? "{$dsn->host}:{$dsn->port}" : $dsn->host );
}

/**
 * Pantheon modifications
 */
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && ! isset( $_ENV['LANDO'] ) ) {
	Config::define( 'DB_HOST', $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT'] );
} else {
	/**
	 * URLs
	 */
	Config::define( 'WP_HOME', env( 'WP_HOME' ) );
	Config::define( 'WP_SITEURL', env( 'WP_SITEURL' ) );
	Config::define( 'DB_HOST', env( 'DB_HOST' ) ?: 'localhost' );
}

/**
 * Custom Content Directory
 */
Config::define( 'CONTENT_DIR', '/app' );
Config::define( 'WP_CONTENT_DIR', $webroot_dir . Config::get( 'CONTENT_DIR' ) );
Config::define( 'WP_CONTENT_URL', Config::get( 'WP_HOME' ) . Config::get( 'CONTENT_DIR' ) );

/**
 * Authentication Unique Keys and Salts
 */
Config::define( 'AUTH_KEY', env( 'AUTH_KEY' ) );
Config::define( 'SECURE_AUTH_KEY', env( 'SECURE_AUTH_KEY' ) );
Config::define( 'LOGGED_IN_KEY', env( 'LOGGED_IN_KEY' ) );
Config::define( 'NONCE_KEY', env( 'NONCE_KEY' ) );
Config::define( 'AUTH_SALT', env( 'AUTH_SALT' ) );
Config::define( 'SECURE_AUTH_SALT', env( 'SECURE_AUTH_SALT' ) );
Config::define( 'LOGGED_IN_SALT', env( 'LOGGED_IN_SALT' ) );
Config::define( 'NONCE_SALT', env( 'NONCE_SALT' ) );

/**
 * Custom Settings
 */
Config::define( 'AUTOMATIC_UPDATER_DISABLED', true );
// Disable the plugin and theme file editor in the admin.
Config::define( 'DISALLOW_FILE_EDIT', true );
// Disable plugin and theme updates and installation from the admin.
Config::define( 'DISALLOW_FILE_MODS', true );
// Limit the number of post revisions that WordPress stores (Threespot default: 3).
Config::define( 'WP_POST_REVISIONS', env( 'WP_POST_REVISIONS' ) ?? 3 );
// Threespot: always use HTTPS for wp-admin and logins.
Config::define( 'FORCE_SSL_ADMIN', true );

/**
 * Threespot: settings that differ per environment or are secret.
 *
 * Read from the environment first (.env locally), then from Pantheon Secrets
 * (`terminus secret:site:set <site> <KEY> <value> --scope=web` exposes
 * pantheon_get_secret()), then from the legacy ~/files/private/secrets.json
 * written by the terminus-secrets plugin. Returns null when unset.
 */
$threespot_secrets_json = null;
$threespot_setting = function ( string $key ) use ( &$threespot_secrets_json ) {
	$value = env( $key );
	if ( null !== $value && '' !== $value ) {
		return $value;
	}
	if ( function_exists( 'pantheon_get_secret' ) ) {
		$value = pantheon_get_secret( $key );
		if ( null !== $value && '' !== $value ) {
			return $value;
		}
	}
	if ( null === $threespot_secrets_json ) {
		$threespot_secrets_json = [];
		if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && ! isset( $_ENV['LANDO'] ) ) {
			$file = rtrim( (string) ( env( 'HOME' ) ?: ( $_SERVER['HOME'] ?? '' ) ), '/' ) . '/files/private/secrets.json';
			if ( is_readable( $file ) ) {
				$decoded = json_decode( (string) file_get_contents( $file ), true );
				$threespot_secrets_json = is_array( $decoded ) ? $decoded : [];
			}
		}
	}
	return $threespot_secrets_json[ $key ] ?? null;
};

/**
 * Threespot: premium plugin licenses. Only defined when set, because some
 * plugins (e.g. FacetWP) lock their license field once the constant exists.
 */
foreach ( [ 'ACF_PRO_LICENSE', 'ACP_LICENCE', 'SEARCHWP_LICENSE_KEY', 'FACETWP_LICENSE_KEY' ] as $threespot_license ) {
	$threespot_license_value = $threespot_setting( $threespot_license );
	if ( $threespot_license_value ) {
		Config::define( $threespot_license, $threespot_license_value );
	}
}
unset( $threespot_license, $threespot_license_value );

/**
 * Threespot: Object Cache Pro (Pantheon object cache / Redis).
 * https://docs.pantheon.io/object-cache/wordpress
 * Pantheon injects CACHE_HOST/PORT/PASSWORD once `object_cache` is enabled in
 * pantheon.yml; Lando's `cache` service supplies them locally.
 */
$threespot_ocp = [
	'token' => $threespot_setting( 'OCP_LICENSE' ),
	'host' => env( 'CACHE_HOST' ) ?: '127.0.0.1',
	'port' => env( 'CACHE_PORT' ) ?: 6379,
	'database' => env( 'CACHE_DB' ) ?: 0,
	'password' => env( 'CACHE_PASSWORD' ) ?: null,
	'maxttl' => 86400,
	'timeout' => 2.0,
	'read_timeout' => 2.0,
	'retry_interval' => 100,
	'split_alloptions' => true,
	'prefetch' => true,
	'debug' => false,
	'save_commands' => false,
	'analytics' => [
		'enabled' => true,
		'persist' => false,
		'retention' => 3600,
		'footnote' => true,
	],
	'prefix' => 'ocppantheon',
	'serializer' => 'igbinary',
	'compression' => 'zstd',
	'async_flush' => true,
	'strict' => true,
];

// Lando's PHP image lacks igbinary/zstd; read the token from auth.json there.
if ( isset( $_ENV['LANDO'] ) && 'ON' === $_ENV['LANDO'] ) {
	$threespot_ocp['serializer'] = 'php';
	$threespot_ocp['compression'] = 'none';
	$threespot_auth_json = $root_dir . '/auth.json';
	if ( ! $threespot_ocp['token'] && is_readable( $threespot_auth_json ) ) {
		$threespot_auth = json_decode( (string) file_get_contents( $threespot_auth_json ), true );
		$threespot_ocp['token'] = $threespot_auth['http-basic']['objectcache.pro']['password'] ?? null;
	}
	unset( $threespot_auth_json, $threespot_auth );
}

Config::define( 'WP_REDIS_CONFIG', $threespot_ocp );

// Without Redis (e.g. a new Pantheon site before `terminus redis:enable`),
// the drop-in would fail every request, including `wp core install`.
if ( ! env( 'CACHE_HOST' ) ) {
	Config::define( 'WP_REDIS_DISABLED', true );
}
unset( $threespot_ocp );

/**
 * Debugging Settings
 */
Config::define( 'WP_DEBUG_DISPLAY', false );
Config::define( 'WP_DEBUG_LOG', false );
Config::define( 'SCRIPT_DEBUG', false );
ini_set( 'display_errors', '0' );

/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if ( file_exists( $env_config ) ) {
	require_once $env_config;
}

Config::apply();

/**
 * Bootstrap WordPress
 */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', $webroot_dir . '/wp/' );
}
