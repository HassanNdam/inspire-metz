@php($url = $block['video'] ? (preg_match('/src="([^"]+)"/', $block['video'], $m) ? html_entity_decode($m[1]) : '') : '')

@if ($url)
  <div class="container-content">
    <button type="button" class="group relative block w-full" x-on:click="$store.ui.playVideo(@js($url))">
      <x-frame shape="c" class="aspect-video">
        @if ($block['poster'])
          {!! wp_get_attachment_image($block['poster'], 'full', false, ['class' => 'frame-media transition duration-500 group-hover:scale-[1.02]', 'loading' => 'lazy', 'alt' => '']) !!}
        @else
          <span class="frame-media block bg-dark-blue-900"></span>
        @endif
      </x-frame>
      <span class="play-button absolute left-1/2 top-1/2 -translate-1/2" aria-hidden="true"><x-icon name="play" class="size-7" /></span>
      <span class="sr-only">{{ __('Lire la vidéo', 'inspire-metz') }}</span>
    </button>
  </div>
@endif
