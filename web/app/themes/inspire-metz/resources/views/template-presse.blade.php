{{--
  Template Name: Espace presse
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.page-header')

    {{-- Ordre du site en ligne : communiqués, derniers Jeem, encart Jeem. --}}
    <div class="relative bg-white pb-24 pt-16 lg:pb-32 lg:pt-14">
      <section class="container-list" aria-labelledby="communiques-title">
        <h2 id="communiques-title" class="mx-auto max-w-[43rem] text-center text-h2 font-medium">{{ $fields['liste_titre'] ?? __('Retrouvez tous les communiqués de presse de l’agence Inspire Metz', 'inspire-metz') }}</h2>

        @include('partials.load-more', [
          'id' => 'communiques',
          'next' => $nextUrl($communiques),
          'class' => 'mt-16 grid gap-x-6 gap-y-12 md:grid-cols-2 lg:mt-20',
          'items' => collect($communiques->posts)->map(fn ($post) => '<li>'.view('partials.cards.document', ['post' => $post])->render().'</li>')->implode(''),
        ])
      </section>

      @if ($jeems)
        <section class="container-list mt-28 lg:mt-32" aria-labelledby="jeem-title">
          <h2 id="jeem-title" class="text-center text-h2 font-medium">{{ $fields['jeem_titre'] ?? __('Consultez les derniers Jeem', 'inspire-metz') }}</h2>

          <ul class="mx-auto mt-10 grid justify-center gap-8 sm:grid-cols-3">
            @foreach ($jeems as $post)
              <li>@include('partials.cards.jeem', ['post' => $post])</li>
            @endforeach
          </ul>

          @if (! empty($fields['jeem_lien']['url']))
            <div class="mt-12 text-center">
              <x-button :href="$fields['jeem_lien']['url']" variant="secondary" :external="($fields['jeem_lien']['target'] ?? '') === '_blank'">{{ $fields['jeem_lien']['title'] }}</x-button>
            </div>
          @endif
        </section>
      @endif

      @if (! empty($fields['promo_titre']))
        <section class="container-list mt-28 lg:mt-32">
          <div class="promo-panel relative grid items-center gap-10 bg-dark-blue-100 px-8 py-12 md:grid-cols-2 lg:px-12 lg:py-20">
            <div class="max-w-[36rem]">
              <h2 class="text-[1.5rem] font-medium leading-tight tracking-[-0.016em] lg:text-[2rem] lg:leading-[2.2rem]">{{ $fields['promo_titre'] }}</h2>
              @if (! empty($fields['promo_texte']))
                <p class="mt-5 text-base">{{ $fields['promo_texte'] }}</p>
              @endif
              @if (! empty($fields['promo_lien']['url']))
                <x-button :href="$fields['promo_lien']['url']" size="lg" class="mt-11" :external="($fields['promo_lien']['target'] ?? '') === '_blank'">{{ $fields['promo_lien']['title'] }}</x-button>
              @endif
            </div>

            @if (! empty($fields['promo_image']))
              @php($promoVideo = ! empty($fields['promo_video']) && preg_match('/src="([^"]+)"/', $fields['promo_video'], $m) ? html_entity_decode($m[1]) : null)
              <div class="relative md:-my-28">
                @if ($promoVideo)
                  <button type="button" class="group relative block w-full" x-on:click="$store.ui.playVideo(@js($promoVideo))">
                    {!! wp_get_attachment_image($fields['promo_image'], 'large', false, ['class' => 'w-full shadow-2xl', 'loading' => 'lazy', 'alt' => '']) !!}
                    <span class="play-button absolute left-1/2 top-1/2 -translate-1/2"><x-icon name="play" class="size-7" label="{{ __('Lire la vidéo', 'inspire-metz') }}" /></span>
                  </button>
                @else
                  {!! wp_get_attachment_image($fields['promo_image'], 'large', false, ['class' => 'w-full shadow-2xl', 'loading' => 'lazy']) !!}
                @endif
              </div>
            @endif
          </div>
        </section>
      @endif
    </div>
  @endwhile
@endsection
