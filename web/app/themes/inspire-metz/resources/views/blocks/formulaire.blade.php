{{-- Formulaire en panneau clair incliné (maquettes p. 16-18). --}}
<div class="container-content" id="formulaire">
  <div class="grid gap-12 lg:grid-cols-2 lg:gap-24">
    <div>
      @if ($block['titre'])
        <h2 @class(['text-h2', 'text-white' => $dark])>{{ $block['titre'] }}</h2>
      @endif

      @if ($block['texte'])
        <p class="mt-6 max-w-md text-sm leading-relaxed">{{ $block['texte'] }}</p>
      @endif
    </div>

    <div class="form-panel bg-slate-100 px-6 py-12 text-slate-700 sm:px-12 lg:py-16">
      @includeIf('forms.'.$block['formulaire'])
    </div>
  </div>
</div>
