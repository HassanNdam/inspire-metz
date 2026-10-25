{{-- Panneau du menu plein écran (maquette p. 23). --}}
<div
  id="menu-panel"
  x-data
  x-cloak
  x-show="$store.ui.menu"
  x-transition:enter="transition duration-300 ease-out"
  x-transition:enter-start="opacity-0"
  x-transition:leave="transition duration-200 ease-in"
  x-transition:leave-end="opacity-0"
  x-trap.inert.noscroll="$store.ui.menu"
  @keydown.escape.window="$store.ui.menu = false"
  class="bg-night bg-grid-dark fixed inset-0 z-40 overflow-y-auto"
  role="dialog"
  aria-modal="true"
  aria-label="{{ __('Menu', 'inspire-metz') }}"
>
  <div class="grid min-h-full grid-rows-[1fr_auto] px-4 pb-10 pt-28 md:px-12 lg:grid-cols-2 lg:pt-32">
    <nav class="lg:col-start-2" aria-label="{{ __('Menu complet', 'inspire-metz') }}">
      <ul class="grid gap-3">
        @foreach ($primaryMenu as $item)
          <li x-data="{ open: false }">
            <div class="flex items-center justify-between gap-6">
              <a href="{{ $item['url'] }}" class="text-title font-normal text-white transition hover:text-cyan-300">{!! $item['title'] !!}</a>

              @if ($item['children'])
                <button type="button" class="text-cyan-300 transition" :class="open && 'rotate-90'" @click="open = ! open" :aria-expanded="open">
                  <x-icon name="chevron-right" class="size-7" />
                  <span class="sr-only">{{ __('Afficher les sous-pages', 'inspire-metz') }}</span>
                </button>
              @endif
            </div>

            @if ($item['children'])
              <ul x-show="open" x-collapse class="mt-2 grid gap-2 border-l border-cyan-400/40 pl-5">
                @foreach ($item['children'] as $child)
                  <li><a href="{{ $child['url'] }}" class="text-lead text-white/80 transition hover:text-white">{!! $child['title'] !!}</a></li>
                @endforeach
              </ul>
            @endif
          </li>
        @endforeach
      </ul>

      @if ($secondaryMenu)
        <ul class="mt-12 grid gap-2">
          @foreach ($secondaryMenu as $item)
            <li><a href="{{ $item['url'] }}" class="text-lead text-white transition hover:text-cyan-300">{!! $item['title'] !!}</a></li>
          @endforeach
        </ul>
      @endif
    </nav>

    <div class="mt-16 flex flex-wrap items-end justify-between gap-8 lg:col-span-2 lg:grid lg:grid-cols-2">
      <div class="flex gap-3">
        @foreach ($socials as $social)
          <a href="{{ $social['url'] }}" class="btn-social" target="_blank" rel="noopener"><x-icon :name="$social['network']" :label="$social['label']" /></a>
        @endforeach
      </div>

      <div class="flex flex-wrap items-end gap-x-24 gap-y-4 text-sm text-white/90">
        <address class="not-italic">{!! $contact['address'] !!}</address>
        <a href="{{ \App\Support\Site::tel($contact['phone']) }}" class="font-semibold">{{ $contact['phone'] }}</a>
      </div>
    </div>
  </div>
</div>
