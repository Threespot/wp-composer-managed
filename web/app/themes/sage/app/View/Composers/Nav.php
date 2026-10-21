<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

use function Threespot\Wp\Helpers\get_menu;

class Nav extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array<string>
     */
    protected static $views = [
        'partials.footer',
        'partials.header',
        'partials.nav-primary',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
      return [
        'primary_navigation' => get_menu('primary_navigation'),
        'secondary_navigation' => get_menu('secondary_navigation'),
        'footer_navigation' => get_menu('footer_navigation'),
      ];
    }
}
