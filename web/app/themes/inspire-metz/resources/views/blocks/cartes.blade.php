{{-- Cartes de navigation (maquette p. 8 : « Un territoire avec 46 communes »…). --}}
<div class="container-content">
  <ul class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($block['cartes'] ?: [] as $index => $carte)
      <li>
        <a href="{{ $carte['lien']['url'] ?? '#' }}" class="nav-card group" @if (($carte['lien']['target'] ?? '') === '_blank') target="_blank" rel="noopener" @endif>
          <x-frame :image="$carte['image']" :shape="['a', 'c', 'b'][$index % 3]" size="large" class="aspect-[11/10]" />
          <span class="nav-card-body">
            <span class="text-[clamp(1.375rem,1.7vw,1.875rem)] font-normal leading-tight">{{ $carte['titre'] }}</span>
            <x-icon name="arrow-right" class="size-5 shrink-0 transition group-hover:translate-x-1" />
          </span>
        </a>
      </li>
    @endforeach
  </ul>
</div>
