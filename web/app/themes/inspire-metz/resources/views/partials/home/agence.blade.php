{{-- L'agence : photo biseautée, titre à cheval, texte et soutiens institutionnels. --}}
<section class="relative bg-dark-blue-950 pb-40 pt-16 text-white lg:pb-48 lg:pt-24" aria-labelledby="agence-title">
  <div class="container-content">
    <div class="relative lg:min-h-[55rem]">
      @if (! empty($home['agence_image']))
        <div class="agence-photo -ml-8 aspect-[792/880] max-w-[49.5rem] lg:ml-0">
          {!! wp_get_attachment_image($home['agence_image'], 'large', false, ['class' => 'size-full object-cover', 'loading' => 'lazy']) !!}
        </div>
      @endif

      <h2 id="agence-title" class="mt-10 max-w-[30rem] text-[2rem] font-medium leading-[2.2rem] tracking-[-0.016em] text-white lg:absolute lg:left-[47.25rem] lg:top-16 lg:mt-0 lg:text-[4rem] lg:leading-[4.1875rem]">
        {{ $home['agence_titre'] ?? '' }}
      </h2>

      <div class="mt-8 max-w-[30rem] lg:absolute lg:left-[65.6rem] lg:top-[27.6rem] lg:mt-0">
        <div class="space-y-4 text-base">{!! $home['agence_texte'] ?? '' !!}</div>

        @if (! empty($home['agence_lien']['url']))
          <x-button :href="$home['agence_lien']['url']" class="mt-10">{{ $home['agence_lien']['title'] }}</x-button>
        @endif
      </div>
    </div>

    @if (! empty($home['agence_soutiens']))
      <div class="mt-16 grid border border-white/15 lg:grid-cols-[2fr_1fr_1fr]">
        <div class="agence-soutiens p-8 text-[1.375rem] leading-[1.625rem] lg:px-14 lg:py-12 lg:text-[1.75rem] lg:leading-[2.06rem]">{!! $home['agence_soutiens'] !!}</div>

        @foreach ($home['agence_logos'] ?? [] as $logo)
          <a href="{{ $logo['url'] ?: '#' }}" class="grid place-items-center border-t border-white/15 p-8 transition hover:bg-white/5 lg:border-l lg:border-t-0" target="_blank" rel="noopener">
            {!! wp_get_attachment_image($logo['logo'], 'medium', false, ['class' => 'h-auto w-[8.875rem]', 'loading' => 'lazy']) !!}
          </a>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Biseau blanc vers la section suivante (separator-white du site en ligne). --}}
  <div class="slant-separator absolute inset-x-0 bottom-0 h-16 bg-white" aria-hidden="true"></div>
</section>
