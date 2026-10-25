{{-- Carte vidéo : la lecture s'ouvre dans le lecteur plein écran. --}}
@php($url = get_field('video', $post, false))

<article class="group">
  <button type="button" class="relative block w-full" x-on:click="$store.ui.playVideo(@js($url))">
    <x-frame shape="a" class="aspect-video">
      {!! get_the_post_thumbnail($post, 'large', ['class' => 'frame-media', 'loading' => 'lazy', 'alt' => '']) !!}
    </x-frame>
    <span class="play-button absolute left-1/2 top-1/2 -translate-1/2" aria-hidden="true"><x-icon name="play" class="size-7" /></span>
    <span class="sr-only">{{ __('Lire la vidéo :', 'inspire-metz') }} {{ get_the_title($post) }}</span>
  </button>

  <h3 class="mt-5 text-[1.25rem] font-normal leading-tight tracking-[-0.016em] text-ink lg:text-[1.5rem]">{!! get_the_title($post) !!}</h3>
  @if (has_excerpt($post))
    <p class="mt-1 text-base text-slate-500">{{ get_the_excerpt($post) }}</p>
  @endif
</article>
