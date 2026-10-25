<div class="container-text">
  <figure class="quote-shards relative max-w-[58rem] px-6 py-12 md:px-16">
    <blockquote @class(['text-center text-quote', 'text-ink' => ! $dark, 'text-white' => $dark])>
      {!! nl2br(e($block['texte'])) !!}
    </blockquote>
  </figure>
</div>
