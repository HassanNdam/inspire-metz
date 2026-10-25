{{-- Mosaïque (maquette p. 16) : rangées de deux photos, petite puis grande, puis l'inverse. --}}
<div class="container-site grid gap-4">
  @foreach (array_chunk($block['images'] ?: [], 2) as $row => $pair)
    <ul @class(['grid gap-4', 'md:grid-cols-[1fr_2fr]' => $row % 2 === 0, 'md:grid-cols-[2fr_1fr]' => $row % 2 === 1])>
      @foreach ($pair as $index => $image)
        <li class="aspect-[16/10] md:aspect-auto md:h-[clamp(14rem,22vw,26rem)]">
          <x-frame
            :image="$image"
            :shape="['a', 'b', 'c', 'd'][($row * 2 + $index) % 4]"
            size="large"
            :credit="wp_get_attachment_caption($image) ?: null"
            class="size-full"
          />
        </li>
      @endforeach
    </ul>
  @endforeach
</div>
