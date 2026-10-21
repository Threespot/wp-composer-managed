<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  {{-- FIXME: Add project web fonts --}}
  {{-- Only preload font files that will be used on every page --}}
  @foreach ([
    'plus-jakarta-sans/plus-jakarta-sans-regular.woff2',
    'plus-jakarta-sans/plus-jakarta-sans-italic.woff2',
  ] as $font_name)
    @if ($font_url = Vite::asset("resources/fonts/$font_name"))
      {{-- Note: Fonts require crossorigin="anonymous" https://www.w3.org/TR/css-fonts-4/#sp213 --}}
      <link rel="preload" as="font" crossorigin="anonymous" type="font/woff2" href="{{ $font_url }}">
    @endif
  @endforeach

  {{-- Preconnect to GTM/GA domains --}}
  @if ($is_production && !empty($gtm_id))
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <link rel="preconnect" href="https://www.google-analytics.com">
  @endif

  {{-- Critical inline JS (comes from vendor/threespot/wp-base-config/src/Assets/critical.js) --}}
  @php(do_action('threespot/critical/inline_script'))

  @php(wp_head())

  @if ($is_production && !empty($gtm_id))
    {{-- Google Tag Manager embed --}}
    <script>
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({'gtm.start': new Date().getTime(), event:'gtm.js'});
    </script>
    <script src="https://www.googletagmanager.com/gtm.js?id={{ $gtm_id }}" async></script>

    {{--
      OPTIONAL: “Global site tag” embed (e.g. Google Analytics)
      https://developers.google.com/tag-platform/gtagjs
    --}}
    {{--
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ $gtm_id }}');
    </script>
    <script src="https://www.googletagmanager.com/gtag/js?id={{ $gtm_id }}" async></script>
    --}}
  @endif
</head>
