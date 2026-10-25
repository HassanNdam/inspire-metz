{{--
  Liste paginée « Charger plus ».
  $id : identifiant de la liste, $next : URL de la page suivante, $class : grille, $items : contenu (slot).
--}}
<div data-list="{{ $id }}" data-next="{{ $next }}" x-data="loadMore(@js($next))">
  <ul x-ref="items" data-items class="{{ $class }}">
    {!! $items !!}
  </ul>

  @if ($next)
    <div class="mt-14 text-center" x-show="next">
      <a href="{{ $next }}" class="btn btn-secondary btn-sm" x-on:click.prevent="load" :aria-busy="loading">
        <x-icon name="arrow-right" />
        <span>{{ __('Charger plus', 'inspire-metz') }}</span>
      </a>
    </div>
  @endif
</div>
