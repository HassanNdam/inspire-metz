{{-- Colonnes avec pictogramme (maquette p. 16 : « Qu'est-ce qu'un ambassadeur ? »). --}}
<div class="container-content">
  <div class="grid gap-14 md:grid-cols-2 lg:gap-24">
    @foreach ($block['colonnes'] ?: [] as $colonne)
      <div class="grid grid-cols-[auto_1fr] gap-x-6">
        <span @class(['grid size-11 place-items-center rounded-sm', 'bg-white/10' => $dark, 'bg-slate-100' => ! $dark])>
          @if ($colonne['picto'])
            {!! wp_get_attachment_image($colonne['picto'], 'thumbnail', false, ['class' => 'size-7 object-contain', 'alt' => '']) !!}
          @endif
        </span>
        <div>
          <h2 @class(['text-title font-normal', 'text-white' => $dark])>{{ $colonne['titre'] }}</h2>
          <p @class(['mt-8 max-w-md text-sm leading-relaxed', 'text-white/85' => $dark])>{{ $colonne['texte'] }}</p>
        </div>
      </div>
    @endforeach
  </div>

  @if (! empty($block['lien']['url']))
    <div class="mt-14 text-center">
      <x-button :href="$block['lien']['url']" size="sm">{{ $block['lien']['title'] }}</x-button>
    </div>
  @endif
</div>
