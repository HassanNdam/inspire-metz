<div class="container-text">
  <div class="max-w-(--container-prose)">
    @if ($block['titre'])
      <h3 @class(['text-h3', 'text-white' => $dark])>{{ $block['titre'] }}</h3>
    @endif

    @if ($block['texte'])
      <p class="mt-4">{{ $block['texte'] }}</p>
    @endif

    <ul class="mt-6 grid gap-4">
      @foreach ($block['liens'] ?: [] as $item)
        <li>
          @if ($item['type'] === 'fichier' && $item['fichier'])
            <a href="{{ wp_get_attachment_url($item['fichier']) }}" class="file-link group" target="_blank" rel="noopener">
              @if ($item['vignette'])
                {!! wp_get_attachment_image($item['vignette'], 'thumbnail', false, ['class' => 'h-16 w-12 -rotate-3 object-cover shadow-md', 'alt' => '']) !!}
              @else
                <span class="grid size-10 shrink-0 place-items-center bg-slate-100 text-cyan-500"><x-icon name="file" /></span>
              @endif
              <span>
                <span @class(['block font-semibold group-hover:text-cyan-600', 'text-ink' => ! $dark, 'text-white' => $dark])>{{ $item['libelle'] ?: get_the_title($item['fichier']) }}</span>
                <span class="text-xs text-slate-500">{{ \App\Support\Site::fileMeta($item['fichier']) }}</span>
              </span>
            </a>
          @elseif (! empty($item['lien']['url']))
            @php($external = ($item['lien']['target'] ?? '') === '_blank' || ! str_starts_with($item['lien']['url'], home_url()))
            <x-link-arrow :href="$item['lien']['url']" :external="$external" :light="$dark">{{ $item['lien']['title'] }}</x-link-arrow>
          @endif
        </li>
      @endforeach
    </ul>
  </div>
</div>
