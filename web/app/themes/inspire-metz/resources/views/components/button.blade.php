@props([
  'href' => null,
  'variant' => 'primary',
  'size' => 'md',
  'icon' => 'arrow-right',
  'trailing' => false,
  'external' => false,
  'type' => 'button',
])

@php($class = collect([
  'btn',
  match ($variant) {
    'secondary' => 'btn-secondary',
    'tertiary' => 'btn-tertiary',
    'light' => 'btn-light',
    'outline' => 'btn-outline',
    default => 'btn-primary',
  },
  match ($size) {
    'sm' => 'btn-sm',
    'lg' => 'btn-lg',
    default => null,
  },
])->filter()->implode(' '))

@if ($href)
  <a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => $class]) }}
    @if ($external) target="_blank" rel="noopener" @endif
  >
    @if ($icon && ! $trailing)<x-icon :name="$icon" />@endif
    <span>{{ $slot }}</span>
    @if ($icon && $trailing)<x-icon :name="$icon" />@endif
    @if ($external)<span class="sr-only">{{ __('(nouvel onglet)', 'inspire-metz') }}</span>@endif
  </a>
@else
  <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>
    @if ($icon && ! $trailing)<x-icon :name="$icon" />@endif
    <span>{{ $slot }}</span>
    @if ($icon && $trailing)<x-icon :name="$icon" />@endif
  </button>
@endif
