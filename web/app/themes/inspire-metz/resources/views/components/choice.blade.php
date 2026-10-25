@props([
  'name',
  'value' => '1',
  'type' => 'checkbox',
  'checked' => false,
  'required' => false,
  'live' => false,
])

@php($key = preg_match('/\[([^\]]+)\]$/', $name, $match) ? $match[1] : $name)

<div {{ $attributes->class(['field-choice-wrap']) }}>
  <label class="field-choice">
    <input
      type="{{ $type }}"
      name="{{ $name }}"
      value="{{ $value }}"
      @checked($checked)
      @required($required)
      @if ($live) @change="clear(@js($key))" @endif
    >
    <span>{{ $slot }}</span>
  </label>

  @if ($live && $type === 'checkbox')
    <p class="field-error" x-cloak x-show="errors[@js($key)]" x-text="errors[@js($key)]"></p>
  @endif
</div>
