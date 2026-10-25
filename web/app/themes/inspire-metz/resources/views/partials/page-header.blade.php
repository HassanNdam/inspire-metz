{{-- En-tête de page (site en ligne). $light : fond blanc (contact, 404). --}}
@php
  $light = $light ?? false;
  // Biseau blanc en bas de l'en-tête, sauf si le premier bloc (sombre) le prolonge.
  $blocks = is_singular() && function_exists('get_field') ? (get_field('blocs') ?: []) : [];
  $slant = ! $light && (($blocks[0]['fond'] ?? 'clair') !== 'sombre');
@endphp

<section @class(['page-header relative overflow-hidden pb-16 pt-28 lg:pb-[7.5rem] lg:pt-[10.5rem]', 'lg:pb-[13.5rem]!' => $date, 'bg-dark-blue-950 text-white' => ! $light])>
  @unless ($light)
    <img src="{{ Vite::asset('resources/images/hero/hero-bg.png') }}" alt="" class="absolute inset-x-0 top-0 h-full w-full object-cover object-top" fetchpriority="high">
  @endunless

  {{-- Actualité : colonne centrée de 58rem, comme le corps de l'article. --}}
  <div @class(['relative', 'container-text' => ! $date, 'mx-auto max-w-[58rem] px-6 lg:px-0' => $date])>
    <nav aria-label="{{ __('Fil d’Ariane', 'inspire-metz') }}">
      <ol @class(['flex flex-wrap items-center gap-x-3 gap-y-1 text-xs leading-4 tracking-[-0.016em] lg:text-[0.8125rem]', 'text-white' => ! $light, 'text-slate-500' => $light])>
        @foreach ($breadcrumbs as $crumb)
          <li class="flex items-center gap-3">
            @if (! $loop->first)<span aria-hidden="true" class="h-3 w-px bg-current"></span>@endif
            @if ($crumb['url'])
              <a href="{{ $crumb['url'] }}" @class(['hover:underline', 'opacity-60' => $loop->first])>{{ $crumb['title'] }}</a>
            @else
              <span aria-current="page" @class(['text-ink' => $light])>{{ $crumb['title'] }}</span>
            @endif
          </li>
        @endforeach
      </ol>
    </nav>

    @if ($date)
      <h1 class="mt-4 max-w-[58rem] text-h1 font-semibold tracking-[-0.024em] text-white">{!! $title !!}</h1>
      <p class="mt-6 text-base text-cyan-300">{{ __('Publié le', 'inspire-metz') }} <time datetime="{{ get_the_date('c') }}">{{ $date }}</time></p>
    @else
      <div class="mt-4 grid items-end gap-8 lg:grid-cols-[41.25rem_1fr] lg:gap-[5.5rem]">
        <h1 @class(['text-h1 font-semibold tracking-[-0.024em]', 'text-white' => ! $light])>{!! $title !!}</h1>

        @if ($chapo)
          <p @class(['max-w-[41.25rem] text-lead', 'text-white/90' => ! $light, 'text-slate-700' => $light])>{{ $chapo }}</p>
        @endif
      </div>
    @endif
  </div>

  @if ($slant && ! $bandeau)
    <div class="slant-separator absolute inset-x-0 bottom-0 h-10 bg-white lg:h-16" aria-hidden="true"></div>
  @endif
</section>

@if ($bandeau)
  <div class="relative -mt-px aspect-[1920/700] min-h-64 w-full overflow-hidden bg-dark-blue-950">
    {!! wp_get_attachment_image($bandeau, 'full', false, ['class' => 'size-full object-cover', 'fetchpriority' => 'high', 'alt' => '']) !!}
    @if ($credit = wp_get_attachment_caption($bandeau))
      <span class="absolute bottom-24 right-2 origin-bottom-right -rotate-90 text-[0.625rem] text-white/80">{{ $credit }}</span>
    @endif
    <div class="slant-separator absolute inset-x-0 bottom-0 h-10 bg-white lg:h-16" aria-hidden="true"></div>
  </div>
@endif
