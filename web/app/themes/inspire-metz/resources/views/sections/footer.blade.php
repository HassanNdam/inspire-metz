{{-- Pied de page (maquette p. 6 et site en ligne). --}}
<footer class="site-footer relative mt-auto text-white">
  <div class="bg-night edge-slant-t relative">
    <img
      src="{{ Vite::asset('resources/images/brand/paper-plane.svg') }}"
      alt=""
      class="pointer-events-none absolute left-[70%] top-[calc(var(--slant)-1.5rem)] w-36 lg:w-56"
      loading="lazy"
    >

    <div class="container-site grid gap-14 pb-14 pt-14 lg:grid-cols-2 lg:gap-0 lg:pb-12">
      <div class="lg:mr-[8.3rem] lg:border-r lg:border-white/15">
        <div class="flex flex-wrap gap-8 sm:flex-nowrap lg:gap-20">
          <a href="{{ home_url('/') }}" class="site-logo block w-[6.625rem] shrink-0" aria-label="{{ __('Inspire Metz — accueil', 'inspire-metz') }}">
            {!! \App\Support\Site::svg('brand/logo-inspire-metz.svg') !!}
          </a>

          <div class="grid gap-4 text-base leading-[1.375rem] tracking-[-0.016em]">
            <div>
              <address class="not-italic">{!! $contact['address'] !!}</address>
              <a href="{{ \App\Support\Site::tel($contact['phone']) }}" class="font-bold">{{ $contact['phone'] }}</a>
            </div>

            @foreach ($contact['services'] as $service)
              <div>
                <p class="font-bold">{{ $service['service'] }}</p>
                <a href="{{ \App\Support\Site::tel($service['telephone']) }}">{{ $service['telephone'] }}</a>
              </div>
            @endforeach
          </div>
        </div>

        <img
          src="{{ Vite::asset('resources/images/brand/map-footer.png') }}"
          alt="{{ __('Carte : Metz au cœur de l’Europe, entre Paris, Luxembourg, Sarrebruck, Francfort, Nancy et Strasbourg', 'inspire-metz') }}"
          class="mt-10 w-full max-w-[31.0625rem] lg:ml-[5.375rem]"
          width="497"
          height="340"
          loading="lazy"
        >
      </div>

      <div class="flex flex-col">
        <p class="max-w-[36.25rem] text-[1.375rem] leading-[1.625rem] lg:text-[1.875rem] lg:leading-[2.25rem]">
          {{ $footer['title'] }} <strong class="font-semibold">{{ $footer['accent'] }}</strong>
        </p>
        <p class="mt-4 max-w-[31.25rem] text-base">{{ $footer['text'] }}</p>

        <div class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-4">
          <x-button size="lg" class="font-medium!" @click="$store.ui.newsletter = true">{{ __('S’inscrire', 'inspire-metz') }}</x-button>
          <x-link-arrow :href="$newsletterUrl" light class="text-base font-medium!">{{ __('Voir nos dernières newsletters', 'inspire-metz') }}</x-link-arrow>
        </div>

        <div class="mt-16 flex items-end justify-between gap-6 lg:mt-auto">
          <div>
            @if ($footerMenu)
              <ul class="flex flex-wrap gap-x-6 gap-y-2 text-[0.9375rem] font-semibold">
                @foreach ($footerMenu as $item)
                  <li><a href="{{ $item['url'] }}" class="transition hover:text-cyan-300">{!! $item['title'] !!}</a></li>
                @endforeach
              </ul>
            @endif
            <p class="mt-10 text-[0.9375rem] leading-none tracking-[-0.016em]">© {{ wp_date('Y') }} {{ get_bloginfo('name') }}</p>
          </div>

          <a href="#app" class="grid size-10 shrink-0 place-items-center transition hover:-translate-y-1" aria-label="{{ __('Retour en haut de page', 'inspire-metz') }}">
            <x-icon name="arrow-up" class="size-6" />
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Partenaires : Région à gauche, Euro-Métropole à droite. --}}
  <div class="bg-white text-slate-600">
    <div class="container-site grid text-[0.8125rem] leading-[1.5] lg:grid-cols-2">
      @foreach ([[$footer['regionLogo'], $footer['regionMention']], [$footer['logo'], $footer['mention']]] as $index => [$logo, $mention])
        @if ($logo || $mention)
          <div @class(['flex items-center gap-8 py-8 lg:py-10', 'lg:mr-[8.3rem] lg:border-r lg:border-slate-200' => $index === 0, 'border-t border-slate-200 lg:border-t-0 lg:pl-2' => $index === 1])>
            @if ($logo)
              {!! wp_get_attachment_image($logo, 'medium', false, ['class' => 'h-auto w-28 shrink-0 lg:w-[8.4375rem]', 'loading' => 'lazy']) !!}
            @endif
            <p @class(["max-w-[25rem]" => $index === 0, "max-w-[40rem]" => $index === 1])>{{ $mention }}</p>
          </div>
        @endif
      @endforeach
    </div>
  </div>
</footer>
