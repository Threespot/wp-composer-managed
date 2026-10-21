<?php
/**
 * Per-site image sizes.
 *
 * Read by threespot/wp-base-config's image helpers (see vendor/threespot/
 * wp-base-config/src/Helpers/image.php). The constant lives in the global
 * namespace because the package reads it as the global IMAGE_SIZES.
 */

// Custom sizes that should always be generated (e.g. ACF thumbnails).
add_image_size('sixteen_nine_thumb', 320, 180, true);

// Image sizes consumed by the Better Image Sizes plugin.
// https://wordpress.org/plugins/better-image-sizes/
const IMAGE_SIZES = [
    // 1:1
    'square' => [
        'ratio' => '1:1',
        'widths' => [360, 750, 1080],
        'crop' => 1,
    ],

    // 1:1 scaled, not cropped
    'square_scaled' => [
        'ratio' => '1:1',
        'widths' => [360, 750, 1080, 1280],
        'crop' => 0,
    ],

    // 16:9
    'sixteen_nine' => [
        'ratio' => '16:9',
        'widths' => [640, 960, 1200, 1600, 1824, 2400],
        'crop' => 1,
    ],

    // 3:2
    // 'three_two' => [
    //     'ratio' => '3:2',
    //     'widths' => [630, 960, 1200, 1440],
    //     'crop' => true,
    // ],
];
