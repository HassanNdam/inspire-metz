@props([
  'name',
  'label' => null,
])

{{-- Icônes trait 24px (stroke = currentColor) ; les logos de réseaux sont pleins. --}}
@php($paths = [
  'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
  'arrow-left' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
  'arrow-up' => '<path d="M12 19V5M6 11l6-6 6 6"/>',
  'arrow-up-right' => '<path d="M7 17 17 7M8 7h9v9"/>',
  'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
  'chevron-right' => '<path d="m9 6 6 6-6 6"/>',
  'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
  'close' => '<path d="M6 6l12 12M18 6 6 18"/>',
  'check' => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
  'file' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>',
  'mouse' => '<rect x="7" y="3" width="10" height="18" rx="5"/><path d="M12 7v3"/>',
  'map-pin' => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
  'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
  'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
  'play' => '<path d="M8 5.5v13a.5.5 0 0 0 .77.42l10-6.5a.5.5 0 0 0 0-.84l-10-6.5A.5.5 0 0 0 8 5.5" fill="currentColor" stroke="none"/>',
  'facebook' => '<path d="M13.5 21v-7.5H16l.5-3h-3V8.6c0-.87.3-1.6 1.6-1.6h1.5V4.3A20 20 0 0 0 14.4 4.2c-2.3 0-3.9 1.4-3.9 4V10.5H8v3h2.5V21z" fill="currentColor" stroke="none"/>',
  'linkedin' => '<path d="M6.5 8.5h-3V20h3zM5 7a1.75 1.75 0 1 0 0-3.5A1.75 1.75 0 0 0 5 7m15 6.4c0-3-1.8-4.9-4.4-4.9-1.6 0-2.6.8-3.1 1.6V8.5h-3V20h3v-6c0-1.5.7-2.6 2.1-2.6 1.3 0 2.1.9 2.1 2.6v6h3.3z" fill="currentColor" stroke="none"/>',
  'x' => '<path d="M17.6 3.5h3.1l-6.8 7.7 8 10.3h-6.3l-4.9-6.4-5.6 6.4H2l7.3-8.3L1.7 3.5h6.4l4.4 5.8zm-1.1 16.2h1.7L7.6 5.2H5.8z" fill="currentColor" stroke="none"/>',
  'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6" fill="currentColor"/>',
  'youtube' => '<path d="M22 8.2a3 3 0 0 0-2.1-2.1C18 5.6 12 5.6 12 5.6s-6 0-7.9.5A3 3 0 0 0 2 8.2 31 31 0 0 0 1.6 12 31 31 0 0 0 2 15.8a3 3 0 0 0 2.1 2.1c1.9.5 7.9.5 7.9.5s6 0 7.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .4-3.8 31 31 0 0 0-.4-3.8M10 15V9l5.2 3z" fill="currentColor" stroke="none"/>',
])

<svg
  {{ $attributes->merge(['class' => 'inline-block size-5 shrink-0']) }}
  xmlns="http://www.w3.org/2000/svg"
  viewBox="0 0 24 24"
  fill="none"
  stroke="currentColor"
  stroke-width="1.75"
  stroke-linecap="round"
  stroke-linejoin="round"
  @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" focusable="false" @endif
>{!! $paths[$name] ?? '' !!}</svg>
