<?php
namespace App;

/**
 * Register the navigation menus.
 * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
 */
add_action('after_setup_theme', function() {
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
        'secondary_navigation' => __('Secondary Navigation', 'sage'),
    ]);
}, 20);
