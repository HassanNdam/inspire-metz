{{-- Bandeau de photos des réseaux sociaux, pleine largeur, bord inférieur biseauté. --}}
@if (! empty($home['reseaux_images']))
  <section class="social-strip relative bg-dark-blue-950" aria-label="{{ __('Photos de nos réseaux sociaux', 'inspire-metz') }}">
    <ul class="grid grid-cols-3 lg:grid-cols-6">
      @foreach ($home['reseaux_images'] as $image)
        <li class="aspect-square overflow-hidden">
          {!! wp_get_attachment_image($image, 'medium_large', false, ['class' => 'size-full object-cover transition duration-500 hover:scale-105', 'loading' => 'lazy']) !!}
        </li>
      @endforeach
    </ul>
  </section>
@endif
