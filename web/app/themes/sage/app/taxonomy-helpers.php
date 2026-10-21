<?php
namespace App;

/**
 * Return taxonomy terms based on post type
 * Used by get_custom_post_labels()
 * @param int $post_id
 * @return object
 */
function get_post_type_taxonomy($post_id) {
    $post_type = get_post_type($post_id);

    switch ($post_type) {
        // Map custom taxonomies to custom post types
        // FIXME: Add CPT and taxonomy mappings
        case 'news':
            $post_type = get_the_terms($post_id, 'news_type');
            break;
        // Optional: Example of hiding the custom post type
        // case 'career':
        //     $post_type = null;
        //     break;
        // Set the default taxonomy
        default:
            $post_type = get_the_tags($post_id);
    }

    // Use the first if multiple
    if (is_array($post_type)) {
        $post_type = $post_type[0];
    }

    return $post_type;
}

/**
 * Determine which post type superheader text to display in content listings,
 * and what URL to use if it should link to an archive page.
 * @param int $post_id
 * @return array
 */
function get_custom_post_type($post_id) {
    $post_type = get_post_type($post_id);
    $custom_post_type = [];

    switch ($post_type) {
        case 'page':
            $custom_post_type = false;
            break;
        case 'post':
            $custom_post_type = false;
            break;
        default:
            $custom_post_type['title'] = get_post_type_object($post_type)->labels->singular_name ?? ucwords($post_type);
            $custom_post_type['url'] = get_post_type_archive_link($post_type) ?: null;
        // NOTE: Use this code if not supporting default post type labels
        // default:
        //     $custom_post_type = ['title' => '', 'url' => false];
    }

    return $custom_post_type;
}

/**
 * Determine what post type and taxonomy superheader text to display
 * on content listing components (e.g. Event | Webinar)
 * @param int $post_id
 * @return array
 */
function get_custom_post_labels($post_id) {
    if (!$post_id) {
        return [];
    }

    $primary_label = get_custom_post_type($post_id);
    $secondary_label = [];
    $post_type = get_post_type($post_id);
    $post_type_tax = get_post_type_taxonomy($post_id);

    if ($post_type_tax) {
      $secondary_label['title'] = $post_type_tax->name;
      $secondary_label['url'] = get_term_link($post_type_tax->term_id, $post_type_tax->taxonomy);

      // OPTIONAL: Example of post type without a secondary label
      // if ($post_type == 'example_cpt') {
      //     $secondary_label = false;
      // }
    }

    // OPTIONAL: Post types without a primary label
    // if (in_array($post_type, ['example_cpt'])) {
    //     $primary_label = false;
    // }

    return [
        'primary' => $primary_label,
        'secondary' => $secondary_label,
    ];
}
