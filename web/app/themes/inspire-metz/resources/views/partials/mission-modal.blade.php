{{-- Détail d'une mission en plein écran (maquette p. 21). --}}
<div
  x-data
  x-cloak
  x-show="$store.ui.mission"
  x-trap.inert.noscroll="$store.ui.mission"
  @keydown.escape.window="$store.ui.mission = null"
  x-transition.opacity.duration.300ms
  class="fixed inset-0 z-50 bg-night bg-grid-dark"
  role="dialog"
  aria-modal="true"
>
  @foreach ($missions as $mission)
    <div x-show="$store.ui.mission === @js($mission['id'])" class="grid h-full lg:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)]" aria-labelledby="{{ $mission['id'] }}-title">
      <div class="order-2 overflow-y-auto px-6 pb-12 pt-8 md:px-16 lg:order-1 lg:pt-8">
        <div class="mb-12 flex items-center justify-between gap-6">
          <button type="button" class="inline-flex items-center gap-2 text-sm text-white" @click="$store.ui.mission = null">
            <x-icon name="close" class="size-4" /> {{ __('Retour', 'inspire-metz') }}
          </button>

          @if (! empty($mission['lien']['url']))
            <x-button :href="$mission['lien']['url']" size="sm" icon="arrow-up-right" trailing :external="($mission['lien']['target'] ?? '') === '_blank'">
              {{ $mission['lien']['title'] ?: __('Accéder au site', 'inspire-metz') }}
            </x-button>
          @endif
        </div>

        <p class="mission-label text-lg font-bold uppercase" data-color="{{ $mission['couleur'] }}">{{ $mission['libelle'] }}</p>
        <h2 id="{{ $mission['id'] }}-title" class="mt-4 max-w-lg text-h2 font-normal text-white">{{ $mission['titre'] }}</h2>
        <div class="mt-10 max-w-xl space-y-5 text-base leading-relaxed text-white/90">
          {!! wpautop(esc_html($mission['texte'])) !!}
        </div>
      </div>

      <div class="relative order-1 h-56 lg:order-2 lg:h-full">
        @if ($mission['image'])
          <x-frame :image="$mission['image']" shape="b" size="full" class="mission-modal-image absolute inset-0 h-full!" />
        @endif
      </div>
    </div>
  @endforeach
</div>
