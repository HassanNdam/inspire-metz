{{-- Actualités (page des articles, maquette p. 12). --}}
@extends('layouts.app')

@php
  $page = (int) get_option('page_for_posts');
  $featured = ! is_paged() ? get_posts([
    'post_type' => 'post',
    'numberposts' => 2,
    'meta_key' => 'a_la_une',
    'meta_value' => '1',
  ]) : [];
  $next = $GLOBALS['wp_query']->max_num_pages > max(1, get_query_var('paged')) ? get_next_posts_page_link() : null;
@endphp

@section('content')
  @include('partials.page-header')

  <div class="relative bg-white pb-24 pt-16 lg:pb-28 lg:pt-24">
    <div class="container-list">
      @if ($featured)
        <section aria-labelledby="une-title" class="mb-24">
          <h2 id="une-title" class="text-[1.5rem] font-semibold leading-tight lg:text-[1.875rem]">{{ get_field('une_titre', $page) ?: __('À la une', 'inspire-metz') }}</h2>

          <div class="mt-5 grid items-end gap-6 md:grid-cols-[42.5rem_1fr]">
            @foreach ($featured as $post)
              @include('partials.cards.news', ['post' => $post, 'size' => 'lg'])
            @endforeach
          </div>
        </section>
      @endif

      <section aria-labelledby="liste-title">
        <h2 id="liste-title" class="text-[1.5rem] font-bold leading-tight lg:text-[1.875rem]">{{ get_field('liste_titre', $page) ?: __('Toutes les actualités', 'inspire-metz') }}</h2>

        @if (have_posts())
          @include('partials.load-more', [
            'id' => 'news',
            'next' => $next,
            'class' => 'mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3',
            'items' => collect($GLOBALS['wp_query']->posts)->map(fn ($post) => '<li>'.view('partials.cards.news', ['post' => $post])->render().'</li>')->implode(''),
          ])
        @else
          <p class="mt-10">{{ __('Aucune actualité pour le moment.', 'inspire-metz') }}</p>
        @endif
      </section>
    </div>
  </div>
@endsection
