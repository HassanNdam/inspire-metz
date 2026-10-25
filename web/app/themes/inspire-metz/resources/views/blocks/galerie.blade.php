@php($images = $block['images'] ?: [])

@if ($images)
  <div class="container-content">
    <div
      class="relative overflow-hidden bg-dark-blue-950"
      x-data="slider({{ count($images) }})"
      role="region"
      aria-roledescription="{{ __('carrousel', 'inspire-metz') }}"
      aria-label="{{ __('Galerie photo', 'inspire-metz') }}"
      @keydown.left="prev"
      @keydown.right="next"
    >
      <div class="relative aspect-[16/8]">
        @foreach ($images as $i => $image)
          <figure
            class="absolute inset-0 transition-opacity duration-500"
            :class="index === {{ $i }} ? 'opacity-100' : 'pointer-events-none opacity-0'"
            :aria-hidden="index !== {{ $i }}"
            aria-roledescription="{{ __('diapositive', 'inspire-metz') }}"
          >
            {!! wp_get_attachment_image($image, 'full', false, ['class' => 'size-full object-cover', 'loading' => $i ? 'lazy' : 'eager']) !!}
            @if ($caption = wp_get_attachment_caption($image))
              <figcaption class="photo-credit">{{ $caption }}</figcaption>
            @endif
          </figure>
        @endforeach
      </div>

      @if (count($images) > 1)
        <button type="button" class="slider-tab slider-tab-prev" x-on:click="prev"><x-icon name="arrow-left" label="{{ __('Image précédente', 'inspire-metz') }}" class="size-4" /></button>
        <button type="button" class="slider-tab slider-tab-next" x-on:click="next"><x-icon name="arrow-right" label="{{ __('Image suivante', 'inspire-metz') }}" class="size-4" /></button>

        <div class="absolute inset-x-0 bottom-4 flex justify-center gap-2">
          @foreach ($images as $i => $image)
            <button
              type="button"
              class="size-2 rounded-full transition"
              :class="index === {{ $i }} ? 'bg-white' : 'bg-white/50 hover:bg-white/80'"
              x-on:click="go({{ $i }})"
              :aria-current="index === {{ $i }}"
            ><span class="sr-only">{{ sprintf(__('Image %d', 'inspire-metz'), $i + 1) }}</span></button>
          @endforeach
        </div>
      @endif
    </div>
  </div>
@endif
