{{--
  Rendu du constructeur de page. Les blocs consécutifs de même fond forment
  une section ; chaque nouvelle section monte en biais sur la précédente.
--}}
@php
  $blocks = function_exists('get_field') ? (get_field('blocs') ?: []) : [];
  $bandeau = function_exists('get_field') ? get_field('bandeau') : null;
  $groups = [];

  foreach ($blocks as $block) {
    $dark = ($block['fond'] ?? 'clair') === 'sombre';
    $last = array_key_last($groups);

    if ($last !== null && $groups[$last]['dark'] === $dark) {
      $groups[$last]['blocks'][] = $block;
    } else {
      $groups[] = ['dark' => $dark, 'blocks' => [$block]];
    }
  }

  // Une page sans bloc garde au moins une section claire (contenu classique éventuel).
  $fallback = ! $groups && trim(get_the_content()) !== '';
@endphp

@foreach ($groups as $index => $group)
  {{-- Le premier groupe suit l'en-tête (biseau déjà présent) ; les suivants montent en biais. --}}
  @php($continuesHeader = $index === 0)

  <section @class([
    'relative',
    'text-white' => $group['dark'],
    'bg-night' => $group['dark'] && ! $continuesHeader,
    'bg-dark-blue-950' => $group['dark'] && $continuesHeader,
    'bg-white' => ! $group['dark'],
    'edge-slant-t' => ! $continuesHeader,
    'pb-24 md:pb-32',
    'pt-20 md:pt-28' => ! $continuesHeader,
    'pt-12 lg:pt-20' => $continuesHeader && ! $group['dark'],
  ])>
    <div class="grid gap-20 md:gap-28">
      @foreach ($group['blocks'] as $block)
        @includeIf("blocks.{$block['acf_fc_layout']}", ['block' => $block, 'dark' => $group['dark']])
      @endforeach
    </div>
  </section>
@endforeach

@if ($fallback)
  <section class="edge-slant-t bg-white bg-grid pb-24 pt-20 md:pt-28">
    <div class="container-content">
      <div class="prose-inspire max-w-(--container-prose)">@php(the_content())</div>
    </div>
  </section>
@endif
