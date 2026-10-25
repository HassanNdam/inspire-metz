{{-- Bandeau défilant des communes, lien vers la page du territoire. --}}
@if ($communes)
  <section class="bg-dark-blue-950 pt-10" aria-label="{{ sprintf(__('Les %d communes de l’Euro-Métropole de Metz', 'inspire-metz'), count($communes)) }}">
    <a href="{{ \App\Support\Site::pageUrl('territoire') }}" class="marquee flex h-32 items-center">
      @foreach ([false, true] as $copy)
        <ul class="marquee-track" @if ($copy) aria-hidden="true" @endif>
          @foreach ($communes as $commune)
            <li class="whitespace-nowrap text-2xl font-medium leading-[3rem] text-cyan-300 lg:text-[2rem]">{{ get_the_title($commune) }}</li>
          @endforeach
        </ul>
      @endforeach
    </a>
  </section>
@endif
