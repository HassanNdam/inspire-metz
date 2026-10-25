@php($right = ($block['position'] ?? 'gauche') === 'droite')

<div class="container-content">
  <div class="grid items-center gap-10 md:grid-cols-2 md:gap-16 lg:gap-24">
    @if ($block['image'])
      <x-frame
        :image="$block['image']"
        :shape="$right ? 'b' : 'a'"
        size="large"
        :credit="wp_get_attachment_caption($block['image']) ?: null"
        @class(['aspect-[10/9]', 'md:order-2' => $right])
      />
    @endif

    <div>
      @if ($block['titre'])
        <h2 @class(['text-h2', 'text-white' => $dark])>{{ $block['titre'] }}</h2>
      @endif

      <div @class(['prose-inspire mt-6 text-base', 'prose-on-dark' => $dark])>{!! $block['texte'] !!}</div>

      @if (! empty($block['lien']['url']))
        <x-link-arrow :href="$block['lien']['url']" :external="($block['lien']['target'] ?? '') === '_blank'" :light="$dark" class="mt-6">{{ $block['lien']['title'] }}</x-link-arrow>
      @endif
    </div>
  </div>
</div>
