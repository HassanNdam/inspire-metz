@props([
  'image' => null,
  'src' => null,
  'alt' => '',
  'shape' => 'a',
  'plain' => false,
  'size' => 'large',
  'credit' => null,
])

{{-- Photo « éclat de verre » : accepte un ID de pièce jointe (image) ou une URL (src). --}}
<figure {{ $attributes->class(['frame', "frame-{$shape}", 'frame-plain' => $plain]) }}>
  <div class="relative h-full w-full">
    @if ($image)
      {!! wp_get_attachment_image($image, $size, false, ['class' => 'frame-media', 'loading' => 'lazy']) !!}
    @elseif ($src)
      <img src="{{ $src }}" alt="{{ $alt }}" class="frame-media" loading="lazy">
    @else
      {{ $slot }}
    @endif

    @if ($credit)
      <figcaption class="photo-credit">{{ $credit }}</figcaption>
    @endif
  </div>
</figure>
