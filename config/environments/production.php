<?php
/**
 * Production overrides: Pantheon `live`.
 */

use Roots\WPConfig\Config;

// Hide deprecation notices emitted before WordPress boots. WordPress sets its
// own error_reporting afterwards.
error_reporting( E_ALL ^ E_DEPRECATED );

Config::define( 'WP_DEBUG', false );
Config::define( 'DISALLOW_FILE_MODS', true );

/**
 * Redirect to PRIMARY_DOMAIN when it's set (env var, Pantheon Secret or
 * secrets.json) and the request arrived on another host. Skipped on the CLI
 * so WP-CLI, cron and Quicksilver never redirect.
 */
$primary_domain = isset( $threespot_setting ) ? $threespot_setting( 'PRIMARY_DOMAIN' ) : null;
if (
	$primary_domain
	&& PHP_SAPI !== 'cli'
	&& isset( $_SERVER['HTTP_HOST'] )
	&& $_SERVER['HTTP_HOST'] !== $primary_domain
) {
	if ( extension_loaded( 'newrelic' ) ) {
		// phpcs:ignore PHPCompatibility.Extensions.RemovedExtensions.newrelicRemoved
		newrelic_name_transaction( 'redirect' );
	}
	header( 'HTTP/1.0 301 Moved Permanently' );
	header( 'Location: https://' . $primary_domain . $_SERVER['REQUEST_URI'] );
	exit;
}
unset( $primary_domain );
