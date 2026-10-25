{{--
  Template Name: Newsletters
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.page-header')

    <div class="relative bg-white pb-24 pt-14 lg:pt-20">
      <section class="container-text">
        <div class="callout relative grid items-center gap-10 overflow-hidden bg-night px-8 pt-12 text-white md:grid-cols-2 md:px-14 md:pt-16">
          <div class="pb-12 md:pb-16">
            <h2 class="max-w-[25rem] text-h2 font-medium text-white">{{ $fields['inscription_titre'] ?? __('S’inscrire à la newsletter', 'inspire-metz') }}</h2>
            @if (! empty($fields['inscription_texte']))
              <p class="mt-6 max-w-[28rem] text-base text-white/90">{{ $fields['inscription_texte'] }}</p>
            @endif
            <x-button class="mt-8" x-on:click="$store.ui.newsletter = true">{{ __('S’inscrire à la newsletter', 'inspire-metz') }}</x-button>
          </div>

          @if (! empty($fields['inscription_apercu']))
            <div class="mx-auto h-64 w-full max-w-[29rem] self-end overflow-hidden md:h-[24rem]">
              {!! wp_get_attachment_image($fields['inscription_apercu'], 'large', false, ['class' => 'w-full object-cover object-top', 'loading' => 'lazy', 'alt' => '']) !!}
            </div>
          @endif
        </div>
      </section>

      <section class="container-list mt-28" aria-labelledby="lettres-title">
        <h2 id="lettres-title" class="mx-auto max-w-[43rem] text-center text-h2 font-medium">{{ $fields['liste_titre'] ?? __('Retrouvez ici les newsletters de l’agence Inspire Metz', 'inspire-metz') }}</h2>

        @include('partials.load-more', [
          'id' => 'lettres',
          'next' => $nextUrl($lettres),
          'class' => 'mx-auto mt-10 grid max-w-[64.7rem] items-start gap-5 md:grid-cols-2',
          'items' => collect($lettres->posts)->map(fn ($post) => '<li>'.view('partials.cards.newsletter', ['post' => $post])->render().'</li>')->implode(''),
        ])
      </section>
    </div>
  @endwhile
@endsection
