{{-- Carte document (communiqué, newsletter envoyée) ouverte dans un nouvel onglet. --}}
@php
  $file = get_field('fichier', $post);
  $url = $file ? wp_get_attachment_url($file) : get_field('lien', $post);
  $date = $dateFormat ?? 'F Y';
@endphp

<article class="group relative flex h-full gap-6 rounded-sm border border-slate-200 bg-white px-6 py-6 transition hover:border-cyan-300 hover:shadow-lg hover:shadow-dark-blue-900/5 lg:gap-8 lg:px-8">
  @if (has_post_thumbnail($post))
    <div class="-mt-10 w-20 shrink-0 rotate-[-4deg] shadow-md transition group-hover:rotate-0 lg:w-[7.75rem]">
      {!! get_the_post_thumbnail($post, 'medium', ['class' => 'w-full', 'loading' => 'lazy', 'alt' => '']) !!}
    </div>
  @endif

  <div class="flex min-w-0 flex-1 flex-col">
    <div class="flex items-start justify-between gap-4">
      <h3 class="text-[1.375rem] font-medium leading-[1.1] tracking-[-0.016em] text-ink lg:text-[1.75rem]">
        <a href="{{ $url ?: '#' }}" class="line-clamp-4 after:absolute after:inset-0" target="_blank" rel="noopener">{!! get_the_title($post) !!}</a>
      </h3>
      <x-icon name="arrow-up-right" class="mt-1 size-4 shrink-0 text-cyan-500" />
    </div>

    <p class="mt-2 text-sm text-slate-500 {{ ($excerpt ?? false) ? '' : 'order-last mt-auto pt-4 uppercase' }}">
      <time datetime="{{ get_the_date('c', $post) }}">{{ get_the_date($date, $post) }}</time>
    </p>

    @if ($excerpt ?? false)
      <p class="mt-4 line-clamp-3 text-sm text-ink">{{ get_the_excerpt($post) }}</p>
    @endif
  </div>
</article>
