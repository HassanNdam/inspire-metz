{{--
  Template Name: Contact
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @php($contact = \App\Support\Site::contact())

    <section class="pb-24 pt-32 md:pt-40">
      <div class="container-content grid gap-16 lg:grid-cols-2 lg:gap-24">
        <div>
          <nav aria-label="{{ __('Fil d’Ariane', 'inspire-metz') }}">
            <ol class="flex items-center gap-2 text-xs text-slate-500">
              <li><a href="{{ home_url('/') }}" class="hover:underline">{{ get_bloginfo('name') }}</a></li>
              <li class="flex items-center gap-2"><span aria-hidden="true" class="h-3 w-px bg-current opacity-60"></span><span aria-current="page" class="text-ink">{!! get_the_title() !!}</span></li>
            </ol>
          </nav>

          <h1 class="mt-6 text-h1 font-semibold">{!! get_the_title() !!}</h1>

          @if ($intro = get_field('chapo'))
            <p class="mt-8 max-w-md text-lead text-ink">{{ $intro }}</p>
          @endif

          <div class="mt-8 flex flex-wrap gap-x-12 gap-y-4 text-sm">
            <p class="flex gap-3"><x-icon name="map-pin" class="mt-0.5 size-4 text-cyan-500" /><span>{!! $contact['address'] !!}</span></p>
            <p class="flex gap-3"><x-icon name="phone" class="mt-0.5 size-4 text-cyan-500" /><a href="{{ \App\Support\Site::tel($contact['phone']) }}" class="hover:underline">{{ $contact['phone'] }}</a></p>
          </div>

          <div class="mt-16 max-w-md">
            @include('forms.contact')
          </div>
        </div>

        @if ($map = get_field('carte'))
          <div class="lg:pt-10">
            <x-frame :image="$map" shape="b" size="large" class="aspect-[4/5] w-full" />
          </div>
        @endif
      </div>
    </section>

    @if ($photo = get_field('photo'))
      <div class="contact-photo relative -mb-24 aspect-[16/7] min-h-72 overflow-hidden">
        {!! wp_get_attachment_image($photo, 'full', false, ['class' => 'size-full object-cover', 'loading' => 'lazy', 'alt' => '']) !!}
      </div>
    @endif
  @endwhile
@endsection
