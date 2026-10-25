{{-- Actualités en carrousel (glisser ou défiler), puis encart Espace presse à cheval sur la section suivante. --}}
<section class="relative bg-white pb-[12.625rem] pt-16 lg:pb-[18.75rem] lg:pt-32" aria-labelledby="actualites-title" x-data="carousel">
  <div class="container-content flex flex-wrap items-center justify-between gap-6">
    <h2 id="actualites-title" class="text-section font-medium tracking-[-0.024em]">{{ $home['actualites_titre'] ?? __('Nos actualités', 'inspire-metz') }}</h2>
    <x-button :href="$newsUrl" variant="outline" size="lg" class="hidden sm:inline-flex">{{ __('Voir tout', 'inspire-metz') }}</x-button>
  </div>

  <ul x-ref="track" class="carousel-track mt-10 lg:mt-14">
    @foreach ($news as $post)
      <li class="w-[80vw] shrink-0 snap-start lg:w-[33.94rem]">
        @include('partials.cards.news', ['post' => $post, 'size' => 'sm'])
      </li>
    @endforeach
  </ul>

  <div class="container-content mt-10 sm:hidden">
    <x-button :href="$newsUrl" variant="outline" size="lg">{{ __('Voir tout', 'inspire-metz') }}</x-button>
  </div>
</section>

@include('partials.home.missions')
