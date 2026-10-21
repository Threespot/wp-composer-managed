<!doctype html>
<html class="no-js smooth-scroll" {!! get_language_attributes() !!}>
  @include('partials.head')
  <body @php(body_class())>
    @php(wp_body_open())
    {{-- Google Tag Manager (noscript) --}}
    @if ($is_production && !empty($gtm_id))
      <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm_id }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif
    @php(do_action('get_header'))
    @include('partials.skip-links')
    {{-- Header --}}
    @include('partials.header')
    <main class="Main" id="main">
      {{-- Login form for password-protected pages --}}
      @if ( post_password_required() )
        <div class="l-padding">
          <div class="l-wrap u-richtex">
            {!! get_the_password_form() !!}
          </div>
        </div>
      @else
        @yield('content')
      @endif
    </main>
    {{-- Footer --}}
    @php(do_action('get_footer'))
    @include('partials.footer')
    @php(wp_footer())
  </body>
</html>
