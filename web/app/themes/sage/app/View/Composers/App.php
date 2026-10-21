<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

use function Threespot\Wp\Helpers\is_production;

class App extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array<string>
     */
    protected static $views = [
        '*',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'is_production' => is_production(),
            'site_name' => $this->site_name(),
            'breadcrumb_links' => $this->get_breadcrumb_links(),
            // 'gtm_id' => get_field('gtm_id', 'options'),
        ];
    }

    /**
     * Returns the site name.
     *
     * @return string
     */
    public function site_name()
    {
        return get_bloginfo('name', 'display');
    }

    /**
     * Generate breadcrumb link array
     * @return array|false
     */
    public function get_breadcrumb_links()
    {
        // Add home link by default
        $breadcrumbs = [
            [
                'title' => 'Home',
                'url' => '/'
            ]
        ];

        // OPTIONAL: Check if this is a post type archive
        // if (is_post_type_archive()) {
        //     // Get the post type of the archive
        //     $post_type = get_query_var('post_type');
        //
        //     // The Events Calendar post types
        //     // if (in_array($post_type, ['tribe_events', 'tribe_organizer', 'tribe_venue'])) {
        //     //     $breadcrumbs[] = [
        //     //         'title' => 'Events',
        //     //         'url' => '/events'
        //     //     ];
        //     // }
        //
        //     return $breadcrumbs;
        // }
        // elseif (is_tax()) {
        //     // Get the taxonomy and term of the archive
        //     $taxonomy = get_query_var('taxonomy');
        //     $term = get_query_var('term');
        // }

        // Don’t add anything else for other archives
        if (is_archive()) {
            return $breadcrumbs;
        }

        // Get current page/post
        $post = get_post();

        if (!$post) {
            return false;
        }

        $post_id = $post->ID;
        $post_type = $post->post_type;

        // Pages
        if ($post_type === 'page') {
            // Add parent pages if applicable
            $parent_ids = [];
            $parent_id = $post->post_parent;

            while ($parent_id) {
                // Add the parent ID to the array
                $parent_ids[] = $parent_id;

                // Get the next parent ID
                $parent_post = get_post($parent_id);
                $parent_id = $parent_post->post_parent;
            }

            // Reverse array to start with highest-level parent
            $parent_ids = array_reverse($parent_ids);

            // Add parent pages to breadcrumbs array
            foreach ($parent_ids as $post_id) {
              $breadcrumbs[] = [
                'title' => get_the_title($post_id),
                'url' => get_permalink($post_id),
              ];
            }

            // Optional: Omit breadcrumbs from all other top-level pages
            // if ($post->post_parent == 0) {
            //     return false;
            // }

            return $breadcrumbs;
        }

        // EXAMPLE: Resources
        // if ($post_type == 'resource') {
        //     // Resources archive
        //     $breadcrumbs[] = [
        //         'title' => get_post_type_object($post_type)->labels->name,
        //         'url' => get_post_type_archive_link($post_type)
        //     ];
        //
        //     // Get resource type terms
        //     $terms = get_the_terms($post_id, 'resource_type');// returns false if none
        //
        //     // Get first term
        //     $term = is_array($terms) ? reset($terms) : $terms;
        //
        //     // Add link to resource type archive, if a term exists
        //     if (!empty($term)) {
        //         $breadcrumbs[] = [
        //             'title' => $term->name,
        //             'url' => get_term_link($term->term_id, $term->taxonomy)
        //         ];
        //     }
        //
        //     return $breadcrumbs;
        // }

        // All other post types link to their post type archive
        $breadcrumbs[] = [
            'title' => get_post_type_object($post_type)->labels->name,
            'url' => get_post_type_archive_link($post_type)
        ];

        return $breadcrumbs;
    }
}
