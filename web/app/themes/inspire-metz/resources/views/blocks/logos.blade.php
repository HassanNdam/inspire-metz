<div class="container-content">
  <div class="flex flex-wrap items-end justify-between gap-4">
    @if ($block['titre'])
      <h3 @class(['text-h3', 'text-white' => $dark])>{{ $block['titre'] }}</h3>
    @endif

    @if (! empty($block['lien']['url']))
      <x-link-arrow :href="$block['lien']['url']" :light="$dark" class="text-sm">{{ $block['lien']['title'] }}</x-link-arrow>
    @endif
  </div>

  <ul class="mt-8 grid grid-cols-2 items-center gap-8 sm:grid-cols-3 lg:grid-cols-5">
    @foreach ($block['logos'] ?: [] as $logo)
      <li class="grid place-items-center">
        @if ($logo['url'])<a href="{{ $logo['url'] }}" target="_blank" rel="noopener" class="transition hover:opacity-70">@endif
          {!! wp_get_attachment_image($logo['logo'], 'medium', false, ['class' => 'max-h-16 w-auto object-contain', 'loading' => 'lazy']) !!}
        @if ($logo['url'])</a>@endif
      </li>
    @endforeach
  </ul>
</div>
