@props([
  'name',
  'label',
  'type' => 'text',
  'value' => null,
  'options' => [],
  'placeholder' => null,
  'required' => false,
  'optional' => false,
  'error' => null,
  'autocomplete' => null,
  'live' => false,
])

{{-- « live » : erreurs pilotées par le composant Alpine imForm (clé = nom entre crochets). --}}
@php
  $id = 'field-'.trim(str_replace(['[', ']'], ['-', ''], $name), '-');
  $key = preg_match('/\[([^\]]+)\]$/', $name, $match) ? $match[1] : $name;
  $errorId = "{$id}-error";
  $control = array_filter([
    'id' => $id,
    'name' => $name,
    'aria-invalid' => $error ? 'true' : null,
    'aria-describedby' => $error ? $errorId : null,
  ]);
  $bindings = $live ? sprintf(':aria-invalid="errors[%1$s] ? \'true\' : null" :aria-describedby="errors[%1$s] ? \'%2$s\' : null" @input="clear(%1$s)"', e(json_encode($key)), $errorId) : '';
@endphp

<div {{ $attributes->class(['field']) }}>
  <label for="{{ $id }}" class="field-label">
    {{ $label }}
    @if ($optional)
      <span class="field-optional">({{ __('optionnel', 'inspire-metz') }})</span>
    @endif
  </label>

  @if ($type === 'textarea')
    <textarea
      @foreach ($control as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach
      class="field-control field-textarea"
      @if ($placeholder) placeholder="{{ $placeholder }}" @endif
      @required($required)
      {!! $bindings !!}
    >{{ $value }}</textarea>
  @elseif ($type === 'select')
    <select
      @foreach ($control as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach
      class="field-control field-select"
      @required($required)
      {!! $bindings !!}
    >
      <option value="">{{ $placeholder ?? __('Sélectionner une option', 'inspire-metz') }}</option>
      @foreach ($options as $optionValue => $optionLabel)
        <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
      @endforeach
    </select>
  @else
    <input
      @foreach ($control as $attr => $attrValue) {{ $attr }}="{{ $attrValue }}" @endforeach
      type="{{ $type }}"
      value="{{ $value }}"
      class="field-control"
      @if ($placeholder) placeholder="{{ $placeholder }}" @endif
      @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
      @required($required)
      {!! $bindings !!}
    >
  @endif

  @if ($live)
    <p id="{{ $errorId }}" class="field-error" x-cloak x-show="errors[@js($key)]" x-text="errors[@js($key)]">{{ $error }}</p>
  @elseif ($error)
    <p id="{{ $errorId }}" class="field-error">{{ $error }}</p>
  @endif
</div>
