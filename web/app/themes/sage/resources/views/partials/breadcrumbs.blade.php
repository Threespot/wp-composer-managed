{{--
  Breadcrumbs
  Note: $breadcrumb_links array is generated in App.php
--}}
@if (!empty($breadcrumb_links))
  @php
    $currentPageLink = get_permalink();
    $is_list = count($breadcrumb_links) > 1;
  @endphp
  <nav class="Breadcrumbs {{ $classes ?? '' }}" aria-label="Breadcrumbs">
    {{-- Use list element if more than 1 link --}}
    @if ($is_list)
      <ol class="Breadcrumbs-list" role="list">
    @else
      <div class="Breadcrumbs-list">
    @endif
    @foreach ($breadcrumb_links as $link)
      {{-- Skip the current page link (shouldn’t be added but just in case) --}}
      @if($link['url'] !== $currentPageLink)
        <{!! $is_list ? 'li' : 'div' !!} class="Breadcrumbs-item">
          <a class="Breadcrumbs-link" href="{{ $link['url'] }}">
            {{ $link['title'] }}
          </a>
        </{{ $is_list ? 'li' : 'div' }}>
      @endif
    @endforeach
    @if ($is_list)
      </ol>
    @else
      </div>
    @endif
  </nav>
@endif
