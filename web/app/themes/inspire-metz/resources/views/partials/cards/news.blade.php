{{-- Carte actualité. $size : sm (carrousel de l'accueil) | md (grille) | lg (à la une). --}}
@php($size = $size ?? 'md')

<article class="group relative">
  <div @class([
    'overflow-hidden bg-slate-200',
    'aspect-[520/373]' => $size === 'sm',
    'aspect-[378/261]' => $size === 'md',
    'aspect-[680/487]' => $size === 'lg',
  ])>
    @if (has_post_thumbnail($post))
      {!! get_the_post_thumbnail($post, $size === 'md' ? 'medium_large' : 'large', ['class' => 'size-full object-cover transition duration-500 group-hover:scale-105', 'loading' => 'lazy', 'alt' => '']) !!}
    @endif
  </div>

  <h3 @class([
    'font-normal tracking-[-0.016em] text-ink',
    'mt-5 truncate text-[1.25rem] leading-6 lg:text-[1.625rem] lg:leading-[1.75rem]' => $size === 'sm',
    'mt-2 line-clamp-3 text-[1.25rem] leading-[1.5625rem]' => $size === 'md',
    'mt-3 text-[1.25rem] leading-[1.5625rem] lg:text-[1.5rem] lg:leading-[1.875rem]' => $size === 'lg',
  ])>
    <a href="{{ get_permalink($post) }}" class="after:absolute after:inset-0 hover:text-cyan-600">{!! get_the_title($post) !!}</a>
  </h3>

  <p @class([
    'uppercase',
    'mt-3 text-base text-slate-600 opacity-80' => $size === 'sm',
    'mt-2 text-sm text-gray-500' => $size !== 'sm',
  ])>
    <time datetime="{{ get_the_date('c', $post) }}">{{ get_the_date('d F Y', $post) }}</time>
  </p>
</article>
