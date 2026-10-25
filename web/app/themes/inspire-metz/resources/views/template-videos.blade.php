{{--
  Template Name: Vidéos
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.page-header')

    <div class="relative bg-white pb-24 pt-14 lg:pt-20">
      <section class="container-content" aria-labelledby="videos-title">
        <h2 id="videos-title" class="text-center text-h2 font-medium">{{ $fields['liste_titre'] ?? __('Découvrir nos vidéos', 'inspire-metz') }}</h2>

        @include('partials.load-more', [
          'id' => 'videos',
          'next' => $nextUrl($videos),
          'class' => 'mt-10 grid gap-x-5 gap-y-14 md:grid-cols-2',
          'items' => collect($videos->posts)->map(fn ($post) => '<li>'.view('partials.cards.video', ['post' => $post])->render().'</li>')->implode(''),
        ])
      </section>
    </div>
  @endwhile
@endsection
