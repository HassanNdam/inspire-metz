@props([
  'href',
  'external' => false,
  'light' => false,
])

<a
  href="{{ $href }}"
  {{ $attributes->class(['link-arrow', 'link-arrow-light' => $light]) }}
  @if ($external) target="_blank" rel="noopener" @endif
>
  <x-icon :name="$external ? 'arrow-up-right' : 'arrow-right'" />
  <span>{{ $slot }}</span>
  @if ($external)<span class="sr-only">{{ __('(nouvel onglet)', 'inspire-metz') }}</span>@endif
</a>
