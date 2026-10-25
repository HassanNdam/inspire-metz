{{-- Encart Espace presse (remonte sur les actualités) puis « Nos missions » et réseaux sociaux. --}}
<section class="glow-spot relative bg-dark-blue-950 pb-20 text-white lg:pb-28" aria-labelledby="missions-title">
  <div class="container-content">
    @if (! empty($home['presse_titre']))
      <div class="press-banner relative -mt-24 overflow-hidden bg-cover bg-center px-8 py-12 lg:-mt-28 lg:py-[3.9375rem] lg:pl-[8.4375rem] lg:pr-8" style="background-image: url('{{ Vite::asset('resources/images/hero/presse-background.jpg') }}')">
        <div class="relative z-10 lg:w-1/2">
          <h2 class="max-w-[32.5rem] text-2xl font-medium leading-[1.625rem] tracking-[-0.024em] text-white lg:text-[2.25rem] lg:leading-[2.4375rem]">{{ $home['presse_titre'] }}</h2>
          @if (! empty($home['presse_lien']['url']))
            <x-button :href="$home['presse_lien']['url']" class="mt-6">{{ $home['presse_lien']['title'] }}</x-button>
          @endif
        </div>

        {{-- Mur de couvertures incliné, colonnes qui défilent. --}}
        <div class="press-wall" aria-hidden="true">
          @foreach (array_chunk($pressCovers, (int) ceil(count($pressCovers) / 3) ?: 1) as $column => $covers)
            <div class="press-wall-column" style="--i: {{ $column }}">
              @foreach ([...$covers, ...$covers] as $cover)
                {!! wp_get_attachment_image($cover, 'medium', false, ['class' => 'press-wall-cover', 'loading' => 'lazy', 'alt' => '']) !!}
              @endforeach
            </div>
          @endforeach
        </div>
      </div>
    @endif

    @if ($missions)
      <h2 id="missions-title" class="missions-title mt-20 lg:mt-24">{{ $home['missions_titre'] ?? __('Nos missions', 'inspire-metz') }}</h2>

      <ul class="relative grid gap-8 lg:mt-8 lg:grid-cols-3 lg:gap-12">
        @foreach ($missions as $index => $mission)
          <li @class(['lg:-translate-y-[5.625rem]' => $index === 1])>
            <button type="button" class="mission-card group" x-on:click="$store.ui.openMission(@js($mission['id']))">
              <span class="mission-card-shape">
                {!! wp_get_attachment_image($mission['image'], 'large', false, ['class' => 'size-full object-cover transition duration-700 group-hover:scale-105', 'loading' => 'lazy', 'alt' => '']) !!}
              </span>

              <span class="mission-card-body">
                <span class="mission-label text-xl font-bold uppercase leading-none tracking-[0.032em] lg:text-2xl" data-color="{{ $mission['couleur'] }}">{{ $mission['libelle'] }}</span>
                <span class="mt-1 flex items-end justify-between gap-4">
                  <span class="text-left text-[1.375rem] font-medium leading-6 tracking-[-0.016em] lg:text-[1.875rem] lg:leading-[2.0625rem]">{{ $mission['titre'] }}</span>
                  <img src="{{ Vite::asset('resources/images/hero/fleche-carte.svg') }}" alt="" class="size-6 shrink-0 transition group-hover:-translate-y-1 group-hover:translate-x-1">
                </span>
              </span>
            </button>

            @if ($index === 1)
              <p class="mx-auto mt-10 hidden max-w-[15.5rem] text-center text-base tracking-[-0.016em] opacity-80 lg:block">
                {{ \App\Support\Site::option('missions_baseline') }}
              </p>
            @endif
          </li>
        @endforeach
      </ul>
    @endif

    <div class="mt-20 flex flex-wrap items-end justify-between gap-6 lg:mt-24" id="reseaux">
      <div>
        <p class="text-base font-bold leading-none tracking-[0.032em] text-cyan-300 lg:text-xl">{{ $hashtag }}</p>
        <p class="mt-1 text-[2rem] font-medium leading-[2.1875rem] tracking-[-0.024em] lg:text-[2.5rem] lg:leading-[2.5rem]">{{ $home['reseaux_titre'] ?? __('Suivez-nous sur les réseaux', 'inspire-metz') }}</p>
      </div>

      <ul class="flex gap-4">
        @foreach ($socials as $social)
          <li>
            @if (in_array($social['network'], ['facebook', 'linkedin', 'x', 'instagram'], true))
              <a href="{{ $social['url'] }}" class="block transition hover:opacity-75" target="_blank" rel="noopener">
                <img src="{{ Vite::asset("resources/images/hero/{$social['network']}.svg") }}" alt="{{ $social['label'] }}" class="size-[2.1875rem]">
              </a>
            @else
              <a href="{{ $social['url'] }}" class="btn-social size-[2.1875rem]" target="_blank" rel="noopener"><x-icon :name="$social['network']" :label="$social['label']" /></a>
            @endif
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</section>
