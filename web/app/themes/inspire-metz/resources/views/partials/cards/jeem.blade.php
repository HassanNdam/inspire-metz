{{-- Couverture d'un numéro de Jeem (site en ligne : 20rem de large, légende alignée à gauche). --}}
@php
  $file = get_field('fichier', $post);
  $url = $file ? wp_get_attachment_url($file) : get_field('lien', $post);
  $numero = get_field('numero', $post);
@endphp

<article class="group relative w-full max-w-[25.3rem]">
  <div class="aspect-[320/443] overflow-hidden bg-slate-200 shadow-xl shadow-dark-blue-900/10 transition duration-300 group-hover:-translate-y-2">
    {!! get_the_post_thumbnail($post, 'medium_large', ['class' => 'size-full object-cover', 'loading' => 'lazy', 'alt' => '']) !!}
  </div>

  <h3 class="mt-4 text-lg font-medium leading-7 text-ink">
    <a href="{{ $url ?: '#' }}" class="after:absolute after:inset-0" target="_blank" rel="noopener">
      {{ $numero ? sprintf(__('Jeem #%s', 'inspire-metz'), $numero) : get_the_title($post) }}
    </a>
  </h3>
  <p class="text-sm uppercase text-gray-500">{{ get_field('periode', $post) }}</p>
</article>
