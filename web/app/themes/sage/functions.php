<?php

use Roots\Acorn\Application;

use function Threespot\Wp\Sage\vite_asset_url;

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our theme. We will simply require it into the script here so that we
| don't have to worry about manually loading any of our classes later on.
|
*/

// phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.Found, Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
if (!file_exists($composer = __DIR__.'/vendor/autoload.php')) {
    wp_die(wp_kses_post(__('Error locating autoloader. Please run <code>composer install</code>.', 'sage')));
}

require $composer;

/*
|--------------------------------------------------------------------------
| Register The Bootloader
|--------------------------------------------------------------------------
|
| The first thing we will do is schedule a new Acorn application container
| to boot when WordPress is finished loading the theme. The application
| serves as the "glue" for all the components of Laravel and is
| the IoC container for the system binding all of the various parts.
|
*/

Application::configure()
    ->withProviders([
        App\Providers\ThemeServiceProvider::class,
    ])
    ->boot();

/*
|--------------------------------------------------------------------------
| Threespot Sage theme config files
|--------------------------------------------------------------------------
|
| Shared WordPress configuration (admin polish, login styling, asset defer
| policy, helpers, etc.) is provided by the threespot/wp-base-config
| Composer package, loaded as an MU-plugin. The files below are intentionally
| per-site:
|   - image-sizes.php  : project-specific image dimensions for Better Image Sizes
|   - theme-assets.php : enqueues for main.scss/main.js/critical.scss
|   - legacy-helpers.php : App\* shims so existing Blade templates keep working
|
*/

require_once __DIR__ . '/app/admin-config.php';
require_once __DIR__ . '/app/block-config.php';
require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/image-sizes.php';
require_once __DIR__ . '/app/menus.php';
require_once __DIR__ . '/app/taxonomy-helpers.php';
require_once __DIR__ . '/app/theme-assets.php';

/*
|--------------------------------------------------------------------------
| Config threespot/wp-base-config
|--------------------------------------------------------------------------
|
| The package can't call Vite::asset() directly (it must also work on
| non-Sage themes), so it fires filters at every enqueue point and the
| theme supplies the URL. See the package README for the full filter list.
|
*/

// Asset URLs — Vite resolves them to hashed paths
add_filter('threespot/login/css_url', fn() => vite_asset_url('resources/styles/login.scss'));
add_filter('threespot/admin/all_css_url', fn() => vite_asset_url('resources/styles/admin-all.scss'));
add_filter('threespot/admin/fields_css_url', fn() => vite_asset_url('resources/styles/admin-fields.scss'));
add_filter('threespot/blocks/editor_css_urls', fn() => [
    vite_asset_url('resources/styles/gutenberg.scss'),
    vite_asset_url('resources/styles/main.scss'),
]);
add_filter('threespot/blocks/editor_js_urls', fn() => [
    vite_asset_url('resources/scripts/block-defaults.jsx'),
    vite_asset_url('resources/scripts/gutenberg.js'),
]);

// Optional config

// Local jQuery path (relative to the theme's resources/ dir). wp-base-config copies
// this file to public/build/assets/resources/<path> and derives the URL from it.
// Must match the jqueryPath passed to threespotViteBase() in vite.config.js.
add_filter('threespot/assets/jquery_path', fn() => 'scripts/lib/jquery-4.0.0.min.js');

// Define which JS files should have type="module" applied on the <script> tag
// NOTE: We’re adding this to main.js to prevent old browsers from running it (i.e. IE 11)
add_filter('threespot/assets/module_script_handles', fn($handles) => [...$handles, 'main-js']);

// Treat any @threespot.com user as an internal user for admin bar customizations
add_filter('threespot/admin/is_internal_user', function ($is_internal, $user) {
    $email = (string) ($user->user_email ?? '');
    return $user && str_contains($email, '@threespot.com');
}, 10, 2);
