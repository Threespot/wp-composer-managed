<?php
// Defaults live in threespot/wp-base-config
// https://github.com/Threespot/wp-base-config/blob/main/src/Sage/prettify.php
$config = array_replace_recursive(
    Threespot\Wp\Sage\prettify_config(),
    [
        // OPTIONAL: Custom overrides for this project
        // 'nice-search' => [
        //     'enabled' => false // Note: Yoast also has a toggle for “Redirect pretty URLs to ‘raw’ formats”
        // ]
    ]
);

// OPTIONAL: Override the relative-urls hooks list (should not be needed but just an FYI)
// This list has numeric keys, so it must be assigned wholesale here rather
// than merged inside array_replace_recursive() above (a recursive merge would
// overwrite by index and leave the unspecified default hooks in place).
// $config['relative-urls']['hooks'] = [
//     'the_permalink',
//     'wp_get_attachment_url',
// ];

return $config;
