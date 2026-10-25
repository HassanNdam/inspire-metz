{{--
  Hero + territoire (site en ligne) : au défilement, l'éclat vidéo du haut
  s'agrandit jusqu'à remplir l'écran et révèle la présentation du territoire,
  pendant que le titre et les trois éclats photo remontent.
  Desktop : scène collante pilotée par --p (0 → 1). Mobile : blocs empilés.
--}}
@php
  $fragments = ['organiser' => 'fragment-pink', 'investir' => 'fragment-orange', 'visiter' => 'fragment-green'];
  $video = $home['territoire_video'] ?? null;
@endphp

<section class="home-hero" x-data="heroScroll" aria-labelledby="home-title">
  {{-- Scène collante : fond, éclat vidéo et contenu territoire. --}}
  <div class="hero-stage">
    <img src="{{ Vite::asset('resources/images/hero/hero-bg.png') }}" alt="" class="hero-bg" fetchpriority="high">

    <div class="hero-video-shard">
      @if ($video)
        <video class="hero-video" autoplay muted loop playsinline preload="metadata" @if (! empty($home['territoire_image'])) poster="{{ wp_get_attachment_image_url($home['territoire_image'], 'large') }}" @endif>
          <source src="{{ $video }}" type="video/mp4">
        </video>
      @elseif (! empty($home['territoire_image']))
        {!! wp_get_attachment_image($home['territoire_image'], 'full', false, ['class' => 'hero-video', 'alt' => '']) !!}
      @endif

      <div class="hero-territoire" :class="revealed && 'is-revealed'">
        <h2 class="hero-territoire-title">{{ $home['territoire_titre'] ?? '' }}</h2>

        <div class="flex flex-wrap items-end justify-between gap-10">
          <div class="max-w-[30.8rem]">
            <div class="space-y-6 text-base">{!! $home['territoire_texte'] ?? '' !!}</div>

            @if (! empty($home['territoire_lien']['url']))
              <x-button :href="$home['territoire_lien']['url']" class="mt-10">{{ $home['territoire_lien']['title'] }}</x-button>
            @endif
          </div>

          @if (! empty($home['territoire_video_lien']['url']))
            <a href="{{ $home['territoire_video_lien']['url'] }}" class="group block w-full max-w-[24rem] text-center">
              @if (! empty($home['territoire_video_image']))
                {!! wp_get_attachment_image($home['territoire_video_image'], 'medium_large', false, ['class' => 'aspect-[384/217] w-full object-cover transition duration-500 group-hover:scale-[1.02]', 'loading' => 'lazy', 'alt' => '']) !!}
              @endif
              <span class="mt-4 block text-2xl font-semibold leading-none">{{ $home['territoire_video_lien']['title'] }}</span>
            </a>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Premier écran : titre et éclats, qui défilent au-dessus de la scène. --}}
  <div class="hero-intro" x-data="parallax">
    <img src="{{ Vite::asset('resources/images/hero/hero-bg.png') }}" alt="" class="hero-bg lg:hidden">
    <img src="{{ Vite::asset('resources/images/hero/light-3.svg') }}" alt="" class="hero-light hero-light-3">
    <img src="{{ Vite::asset('resources/images/hero/light-2.svg') }}" alt="" class="hero-light hero-light-2">
    <img src="{{ Vite::asset('resources/images/hero/light-1.svg') }}" alt="" class="hero-light hero-light-1">

    <h1 id="home-title" class="hero-title">
      {{-- Un retour à la ligne dans le champ = une ligne du titre. --}}
      @foreach (preg_split('/\R/', $home['hero_titre'] ?? __('Découvrez l’Euro-Métropole de Metz,', 'inspire-metz')) as $line)
        <span class="block font-semibold">{{ $line }}</span>
      @endforeach
      @foreach (preg_split('/\R/', $home['hero_titre_accent'] ?? __('territoire innovant & durable', 'inspire-metz')) as $line)
        <span class="block text-cyan-300">{{ $line }}</span>
      @endforeach
    </h1>

    <div class="hero-shards">
      @foreach ($shards as $shard)
        @php($mission = $shard['mission'])
        @php($link = $mission['lien']['url'] ?? null)

        <div class="hero-shard hero-shard-{{ $shard['position'] }}">
          <{{ $link ? 'a' : 'div' }} @if ($link) href="{{ $link }}" target="_blank" rel="noopener" @endif class="hero-shard-link group">
            <span class="hero-shard-shape">
              {!! wp_get_attachment_image($shard['image'], 'large', false, ['class' => 'hero-shard-img', 'alt' => '', 'fetchpriority' => 'high']) !!}
              <img src="{{ Vite::asset("resources/images/hero/reflection-{$shard['position']}.svg") }}" alt="" class="hero-shard-reflection">
            </span>

            @if ($mission)
              <span class="hero-shard-caption">
                <span class="flex items-center gap-3">
                  <img src="{{ Vite::asset('resources/images/hero/'.($fragments[$mission['couleur']] ?? 'fragment-pink').'.svg') }}" alt="" class="w-6">
                  <span class="text-[1.875rem] font-medium leading-tight tracking-[-0.016em]">{{ $mission['libelle'] }}</span>
                </span>
                <span class="hero-shard-text">{{ $mission['titre'] }}</span>
              </span>

              @if ($link)
                <span class="hero-shard-cta" aria-hidden="true">{!! __('Voir<br>le site', 'inspire-metz') !!}</span>
                <span class="sr-only">{{ $mission['libelle'] }} : {{ $mission['titre'] }} ({{ __('nouvel onglet', 'inspire-metz') }})</span>
              @endif
            @endif
          </{{ $link ? 'a' : 'div' }}>
        </div>
      @endforeach
    </div>

    <p class="hero-hint" aria-hidden="true">
      <img src="{{ Vite::asset('resources/images/hero/move-cursor.svg') }}" alt="" class="mx-auto mb-2 w-5">
      {!! __('Déplacer<br>votre curseur<br>pour explorer', 'inspire-metz') !!}
    </p>
  </div>
</section>
