<!doctype html>
<html @php(language_attributes())>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body @php(body_class('bg-grid'))>
    @php(wp_body_open())

    <div id="app" x-data class="flex min-h-screen flex-col overflow-x-clip">
      <a class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:bg-white focus:px-4 focus:py-2 focus:text-ink" href="#main">
        {{ __('Aller au contenu', 'inspire-metz') }}
      </a>

      @include('sections.header')

      <main id="main" class="main flex-1">
        @yield('content')
      </main>

      @include('sections.footer')
    </div>

    @include('partials.newsletter-modal')
    @include('partials.mission-modal')
    @include('partials.video-lightbox')

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
