@php($current = collect($languages)->firstWhere('current', true) ?? $languages[0])

@if (count($languages) > 1)
  <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
    <button type="button" class="site-lang" @click="open = ! open" :aria-expanded="open">
      <span class="sr-only">{{ __('Langue :', 'inspire-metz') }}</span>{{ $current['code'] }}
      <x-icon name="chevron-down" class="size-3" />
    </button>

    <ul x-cloak x-show="open" x-transition.opacity class="site-dropdown absolute right-0 top-full mt-2 min-w-0 text-center">
      @foreach ($languages as $language)
        <li>
          <a href="{{ $language['url'] }}" hreflang="{{ strtolower($language['code']) }}" @class(['site-dropdown-link px-3', 'is-active' => $language['current']])>
            {{ $language['code'] }}
          </a>
        </li>
      @endforeach
    </ul>
  </div>
@else
  <span class="site-lang" lang="fr">{{ $current['code'] }}</span>
@endif
