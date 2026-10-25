@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.page-header')

    <div class="relative bg-white pb-24">
      @if (has_post_thumbnail())
        <div class="container-text relative z-10 -mt-12 lg:-mt-[10rem]">
          <figure>
            {!! get_the_post_thumbnail(null, 'full', ['class' => 'aspect-[1409/712] w-full object-cover', 'fetchpriority' => 'high']) !!}
            @if ($caption = get_the_post_thumbnail_caption())
              <figcaption class="mt-2 text-right text-xs text-slate-500">{{ $caption }}</figcaption>
            @endif
          </figure>
        </div>
      @endif

      <div class="article-body mx-auto mt-16 max-w-[58rem] px-6 lg:mt-32 lg:px-0">
        @if (get_field('blocs'))
          @foreach (get_field('blocs') as $block)
            <div class="mt-16 first:mt-0">
              @includeIf("blocks.{$block['acf_fc_layout']}", ['block' => $block, 'dark' => false, 'narrow' => true])
            </div>
          @endforeach
        @else
          <div class="container-content">
            <div class="prose-inspire">@php(the_content())</div>
          </div>
        @endif

        <div class="container-content mt-16">
          <x-link-arrow :href="get_permalink(get_option('page_for_posts')) ?: home_url('/')">{{ __('Toutes les actualités', 'inspire-metz') }}</x-link-arrow>
        </div>
      </div>
    </div>
  @endwhile
@endsection
