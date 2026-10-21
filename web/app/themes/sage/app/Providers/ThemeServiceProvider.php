<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

use function Threespot\Wp\Sage\register_blade_directives;

class ThemeServiceProvider extends SageServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
    public function register()
    {
        parent::register();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        register_blade_directives();

        parent::boot();
    }
}
