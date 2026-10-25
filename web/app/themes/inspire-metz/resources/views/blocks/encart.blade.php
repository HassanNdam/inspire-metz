<div class="container-content">
  <div class="callout relative grid items-center gap-10 overflow-hidden bg-night px-8 py-14 text-white md:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)] md:px-14 md:py-16">
    <div>
      @if ($block['titre'])
        <h2 class="text-h2 text-white">{{ $block['titre'] }}</h2>
      @endif

      @if ($block['texte'])
        <p class="mt-6 max-w-2xl text-base text-white/90">{{ $block['texte'] }}</p>
      @endif

      @if (! empty($block['lien']['url']))
        <x-button :href="$block['lien']['url']" class="mt-8" :external="($block['lien']['target'] ?? '') === '_blank'">{{ $block['lien']['title'] }}</x-button>
      @endif
    </div>

    @if ($block['image'])
      <div class="relative">
        {!! wp_get_attachment_image($block['image'], 'large', false, ['class' => 'mx-auto max-h-80 w-auto rotate-2 shadow-2xl', 'loading' => 'lazy']) !!}
      </div>
    @endif
  </div>
</div>
