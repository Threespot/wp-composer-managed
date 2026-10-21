<?php
/**
 * Staging overrides: Pantheon `test`.
 */

use Roots\WPConfig\Config;

// Hide deprecation notices emitted before WordPress boots (e.g. from
// autoloaded libraries). WordPress sets its own error_reporting afterwards.
error_reporting( E_ALL ^ E_DEPRECATED );

Config::define( 'WP_DEBUG', false );
Config::define( 'DISALLOW_FILE_MODS', true );
Config::define( 'DISALLOW_INDEXING', true );
