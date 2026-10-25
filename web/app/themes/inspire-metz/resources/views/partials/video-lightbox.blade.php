{{-- Lecteur vidéo : ouvert par $store.ui.playVideo(url) depuis les cartes vidéo. --}}
<div
  x-data
  x-cloak
  x-show="$store.ui.video"
  x-trap.inert.noscroll="$store.ui.video"
  @keydown.escape.window="$store.ui.video = null"
  x-transition.opacity
  class="fixed inset-0 z-50 grid place-items-center bg-dark-blue-950/90 p-4 backdrop-blur"
  role="dialog"
  aria-modal="true"
  aria-label="{{ __('Lecteur vidéo', 'inspire-metz') }}"
  @click.self="$store.ui.video = null"
>
  <button type="button" class="btn-icon absolute right-5 top-5" @click="$store.ui.video = null">
    <x-icon name="close" label="{{ __('Fermer la vidéo', 'inspire-metz') }}" />
  </button>

  <div class="aspect-video w-full max-w-6xl bg-black">
    <template x-if="$store.ui.video">
      <iframe :src="$store.ui.video" class="size-full" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen title="{{ __('Vidéo', 'inspire-metz') }}"></iframe>
    </template>
  </div>
</div>
