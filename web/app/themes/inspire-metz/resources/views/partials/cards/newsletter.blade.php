{{-- Newsletter envoyée (site en ligne) : numéro, date, sommaire complet. --}}
@php($url = get_field('lien', $post))

<article class="group relative rounded-sm border border-slate-200 bg-white p-6 transition hover:border-cyan-300 hover:shadow-lg hover:shadow-dark-blue-900/5">
  <div class="flex items-start justify-between gap-4">
    <h3 class="text-lg font-semibold leading-6 text-ink">
      <a href="{{ $url ?: '#' }}" class="after:absolute after:inset-0" target="_blank" rel="noopener">{!! get_the_title($post) !!}</a>
    </h3>
    <x-icon name="arrow-up-right" class="mt-1 size-4 shrink-0 text-cyan-500" />
  </div>

  <p class="text-xs text-slate-500">
    <time datetime="{{ get_the_date('c', $post) }}">{{ get_the_date('j F Y', $post) }}</time>
  </p>

  <p class="mt-4 text-sm leading-relaxed text-ink">{{ get_the_excerpt($post) }}</p>
</article>
