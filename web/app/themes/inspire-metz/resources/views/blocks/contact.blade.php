<div class="container-text">
  <div class="max-w-(--container-prose)">
    @if ($block['titre'])
      <h2 @class(['text-h2', 'text-white' => $dark])>{{ $block['titre'] }}</h2>
    @endif

    @if ($block['texte'])
      <p class="mt-6">{{ $block['texte'] }}</p>
    @endif

    <div class="mt-8 grid gap-3 text-base">
      @if ($block['intitule'])
        <p @class(['font-semibold', 'text-ink' => ! $dark])>{{ $block['intitule'] }}</p>
      @endif
      @if ($block['adresse'])
        <address class="not-italic">{!! $block['adresse'] !!}</address>
      @endif
      @if ($block['telephone'])
        <p>{{ __('Tél :', 'inspire-metz') }} <a href="{{ \App\Support\Site::tel($block['telephone']) }}" class="hover:underline">{{ $block['telephone'] }}</a></p>
      @endif
      @if ($block['email'])
        <p>{{ __('E-mail :', 'inspire-metz') }} <a href="mailto:{{ antispambot($block['email']) }}" class="hover:underline">{{ antispambot($block['email']) }}</a></p>
      @endif
    </div>
  </div>
</div>
