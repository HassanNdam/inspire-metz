{{-- En-tête (maquette p. 5) : masqué en descendant, réaffiché en remontant. --}}
<header
  x-data="siteHeader"
  :class="{ 'is-hidden': hidden && ! $store.ui.menu, 'is-solid': solid || $store.ui.menu }"
  @class(['site-header', 'site-header-light' => $headerTheme === 'light'])
>
  <div class="flex items-center gap-6 px-6 py-3 lg:px-12 lg:py-4">
    <a href="{{ home_url('/') }}" class="flex shrink-0 items-center gap-6" aria-label="{{ __('Inspire Metz — accueil', 'inspire-metz') }}">
      <span class="site-logo block w-26 lg:w-[9.375rem]">{!! \App\Support\Site::svg('brand/logo-inspire-metz.svg') !!}</span>
      <span class="site-baseline hidden w-[149px] text-[11px] leading-[13.2px] opacity-80 md:block">
        {{ __('Site officiel d’Inspire Metz, agence d’attractivité de l’Euro-Métropole de Metz', 'inspire-metz') }}
      </span>
    </a>

    @if ($primaryMenu)
      <nav class="ml-auto hidden transition-opacity lg:block" :class="$store.ui.menu && 'pointer-events-none opacity-0'" aria-label="{{ __('Navigation principale', 'inspire-metz') }}">
        <ul class="flex items-center gap-2">
          @foreach ($primaryMenu as $item)
            <li
              class="relative"
              x-data="{ open: false }"
              @mouseenter="open = true"
              @mouseleave="open = false"
              @focusin="open = true"
              @focusout="$el.contains($event.relatedTarget) || (open = false)"
              @keydown.escape="open = false"
            >
              <a
                href="{{ $item['url'] }}"
                @class(['site-nav-link', 'is-active' => $item['active']])
                @if ($item['children']) aria-haspopup="true" :aria-expanded="open" @endif
              >{!! $item['title'] !!}</a>

              @if ($item['children'])
                <div x-cloak x-show="open" x-transition.opacity.duration.150ms class="absolute left-0 top-full">
                  <ul class="site-dropdown">
                    @foreach ($item['children'] as $child)
                      <li>
                        <a href="{{ $child['url'] }}" @class(['site-dropdown-link', 'is-active' => $child['active']]) @if ($child['target']) target="{{ $child['target'] }}" @endif>
                          {!! $child['title'] !!}
                        </a>
                      </li>
                    @endforeach
                  </ul>
                </div>
              @endif
            </li>
          @endforeach
        </ul>
      </nav>
    @endif

    <div class="ml-auto flex items-center gap-4 lg:ml-2">
      <button
        type="button"
        class="btn-icon site-burger"
        @click="$store.ui.toggleMenu()"
        :aria-expanded="$store.ui.menu"
        aria-controls="menu-panel"
      >
        <x-icon name="menu" x-show="! $store.ui.menu" />
        <x-icon name="close" x-cloak x-show="$store.ui.menu" />
        <span class="sr-only" x-text="$store.ui.menu ? '{{ __('Fermer le menu', 'inspire-metz') }}' : '{{ __('Ouvrir le menu', 'inspire-metz') }}'"></span>
      </button>

      @include('sections.language-switcher')
    </div>
  </div>
</header>

@include('sections.menu-panel')
