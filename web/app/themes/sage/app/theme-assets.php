<?php
/**
 * Theme-owned asset enqueues.
 *
 * Script `defer` policy, jQuery override, and oEmbed cleanup are handled by
 * threespot/wp-base-config (AssetConfig). This file only owns the assets that
 * belong to this specific theme: main.scss, main.js, and the inlined critical
 * stylesheet.
 */

namespace App;

use function Threespot\Wp\Sage\inline_critical_css;
use function Threespot\Wp\Sage\vite_asset_url;

add_action('wp_enqueue_scripts', function () {
    // phpcs:ignore WordPress.WP.EnqueuedResourceParameters
    wp_enqueue_style('main-css', vite_asset_url('resources/styles/main.scss'), [], '');

    // phpcs:ignore WordPress.WP.EnqueuedResourceParameters
    wp_enqueue_script('main-js', vite_asset_url('resources/scripts/main.js'), [], '', true);
}, 100);

add_action('wp_head', function () {
    inline_critical_css('resources/styles/critical.scss');
}, 2);
