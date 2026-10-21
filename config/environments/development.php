<?php
/**
 * Development overrides: Lando, Pantheon `dev`, and every multidev
 * (anything that isn't `test` or `live`).
 */

use Roots\WPConfig\Config;
use function Env\env;

Config::define( 'WP_DEBUG', env( 'WP_DEBUG' ) ?? true );
Config::define( 'WP_DEBUG_LOG', env( 'WP_DEBUG_LOG' ) ?? true );
Config::define( 'WP_DEBUG_DISPLAY', false );
Config::define( 'SCRIPT_DEBUG', env( 'SCRIPT_DEBUG' ) ?? false );
Config::define( 'SAVEQUERIES', false );
Config::define( 'DISALLOW_INDEXING', true );

if ( env( 'IS_LOCAL' ) ) {
	Config::define( 'IS_LOCAL', true );
}
