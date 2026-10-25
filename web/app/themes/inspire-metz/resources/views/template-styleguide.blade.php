{{--
  Template Name: Design system
--}}

@extends('layouts.app')

@php
  $palettes = [
    'Dark Blue' => ['dark-blue', [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950]],
    'Cyan' => ['cyan', [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950]],
    'Grayscale' => ['slate', [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950]],
    'Meeting You' => ['meeting-you', [300, 400, 500, 700, 800]],
    'Invest In' => ['invest-in', [300, 400, 500, 700, 800]],
    'Office de tourisme' => ['tourisme', [300, 400, 500, 700, 800]],
  ];

  $type = [
    ['text-display font-semibold', 'Display — 64px SemiBold', 'Découvrez l’Eurométropole de Metz'],
    ['text-h1 font-semibold', 'H1 — 72px SemiBold', 'Titre de niveau 1'],
    ['text-section font-medium', 'Section — 64px Medium', 'Nos actualités'],
    ['text-h2 font-medium', 'H2 — 48px Medium', 'Titre de niveau 2'],
    ['text-title font-medium', 'Titre bloc — 40px Medium', 'Suivez-nous sur les réseaux'],
    ['text-h3 font-medium', 'H3 — 32px Medium', 'Titre de niveau 3'],
    ['text-quote', 'Citation — 32px Regular', 'Fusce at nisi eget dolor rhoncus facilisis.'],
    ['text-stat font-medium', 'Chiffre clé — 56px Medium', '280 000'],
    ['text-lead-lg', 'Chapô large — 28px', 'Inspire Metz est l’agence d’attractivité…'],
    ['text-card', 'Titre de carte — 26px', 'Marché de Noël à Metz'],
    ['text-lead', 'Chapô — 20px', 'Aliquam in bibendum mauris. Sed vitae erat vel velit blandit.'],
    ['text-body', 'Texte courant — 18px', 'Nunc scelerisque tincidunt elit. Vestibulum non mi ipsum.'],
    ['text-base text-slate-600 uppercase', 'Méta — 16px', '21 mars 2024'],
  ];
@endphp

@section('content')
  <section class="bg-night bg-grid-dark pb-16 pt-40">
    <div class="container-content">
      <p class="text-sm text-dark-blue-300">Inspire Metz · Thème Sage</p>
      <h1 class="mt-4 text-h1 font-semibold text-white">Design system</h1>
      <p class="mt-6 max-w-2xl text-lead text-white/80">
        Tokens et composants issus de la maquette. Toute évolution graphique se fait dans
        <code class="text-cyan-300">resources/css</code> et <code class="text-cyan-300">resources/views/components</code>.
      </p>
    </div>
  </section>

  <div class="bg-grid">
    {{-- Couleurs --}}
    <section class="container-content py-20">
      <h2 class="text-h2">Couleurs</h2>

      <div class="mt-10 grid gap-10">
        @foreach ($palettes as $label => [$token, $steps])
          <div>
            <h3 class="mb-4 text-lg font-semibold">{{ $label }}</h3>
            <div class="grid grid-cols-3 gap-3 sm:grid-cols-6 lg:grid-cols-11">
              @foreach ($steps as $step)
                <div>
                  <div class="h-16 rounded-sm border border-slate-200" style="background: var(--color-{{ $token }}-{{ $step }})"></div>
                  <p class="mt-1 text-xs font-semibold text-ink">{{ $step }}</p>
                  <p class="text-xs text-slate-500">--color-{{ $token }}-{{ $step }}</p>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach

        <div>
          <h3 class="mb-4 text-lg font-semibold">Interactions & missions</h3>
          <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
            @foreach (['action', 'action-hover', 'error', 'mission-visiter', 'mission-organiser', 'mission-investir'] as $token)
              <div>
                <div class="h-16 rounded-sm border border-slate-200" style="background: var(--color-{{ $token }})"></div>
                <p class="mt-1 text-xs text-slate-500">--color-{{ $token }}</p>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </section>

    {{-- Typographie --}}
    <section class="container-content py-20">
      <h2 class="text-h2">Typographie — Poppins</h2>
      <p class="mt-4 text-slate-600">Échelle fluide : taille mobile (374px) → taille desktop (1920px).</p>

      <div class="mt-10 divide-y divide-slate-200 border-y border-slate-200">
        @foreach ($type as [$classes, $label, $sample])
          <div class="grid gap-2 py-6 md:grid-cols-[16rem_1fr] md:items-baseline">
            <p class="text-sm text-slate-500">{{ $label }}<br><code class="text-xs">{{ $classes }}</code></p>
            <p class="{{ $classes }} text-ink">{{ $sample }}</p>
          </div>
        @endforeach
      </div>
    </section>

    {{-- Interactions --}}
    <section class="container-content py-20">
      <h2 class="text-h2">Interactions</h2>

      <div class="mt-10 grid gap-8">
        @foreach (['primary' => 'Primary', 'secondary' => 'Secondary', 'tertiary' => 'Tertiary'] as $variant => $label)
          <div class="flex flex-wrap items-center gap-6">
            <p class="w-28 font-semibold text-ink">{{ $label }}</p>
            <x-button :variant="$variant">Button text</x-button>
            <x-button :variant="$variant" size="lg">Button text</x-button>
            <x-button :variant="$variant" size="sm">Button text</x-button>
          </div>
        @endforeach

        <div class="flex flex-wrap items-center gap-6">
          <p class="w-28 font-semibold text-ink">Link</p>
          <x-link-arrow href="#">Link text</x-link-arrow>
          <x-link-arrow href="https://inspire-metz.com" external>Lien externe</x-link-arrow>
        </div>
      </div>
    </section>

    <section class="bg-night bg-grid-dark py-20">
      <div class="container-content">
        <h2 class="text-h2 text-white">Interactions — onDark</h2>

        <div class="mt-10 grid gap-8">
          <div class="flex flex-wrap items-center gap-6">
            <p class="w-28 font-semibold">Primary</p>
            <x-button variant="light">Button text</x-button>
            <x-button variant="light" size="lg">Button text</x-button>
            <x-button variant="light" size="sm">Button text</x-button>
            <x-button>Primary cyan</x-button>
          </div>

          <div class="flex flex-wrap items-center gap-6">
            <p class="w-28 font-semibold">Link</p>
            <x-link-arrow href="#" light>Link text</x-link-arrow>
          </div>

          <div class="flex flex-wrap items-center gap-4">
            <p class="w-28 font-semibold">Icônes</p>
            <button type="button" class="btn-icon"><x-icon name="menu" label="Menu" /></button>
            <button type="button" class="btn-icon"><x-icon name="close" label="Fermer" /></button>
            <a href="#" class="btn-social"><x-icon name="facebook" label="Facebook" /></a>
            <a href="#" class="btn-social"><x-icon name="linkedin" label="LinkedIn" /></a>
            <a href="#" class="btn-social"><x-icon name="x" label="X" /></a>
          </div>
        </div>
      </div>
    </section>

    {{-- Formulaires --}}
    <section class="container-content py-20">
      <h2 class="text-h2">Formulaires</h2>

      <form class="mt-10 grid gap-10 md:grid-cols-2 xl:grid-cols-4" onsubmit="return false">
        <div class="grid content-start gap-5">
          <h3 class="text-lg font-semibold">Inputs</h3>
          <x-field name="sg_text" label="Text" value="Content" />
          <x-field name="sg_empty" label="Text Empty" />
          <x-field name="sg_optional" label="Text" optional />
          <x-field name="sg_error" label="Text Error" value="Cotrent" error="Merci de vérifier ce champ." />
        </div>

        <div class="grid content-start gap-5">
          <h3 class="text-lg font-semibold">Selects</h3>
          <x-field name="sg_select" label="Select" type="select" value="1" :options="[1 => 'Option', 2 => 'Option 2']" />
          <x-field name="sg_select_empty" label="Select Empty" type="select" :options="[1 => 'Option 1', 2 => 'Option 2', 3 => 'Option 3']" placeholder="Select an option" />
        </div>

        <div class="grid content-start gap-5">
          <h3 class="text-lg font-semibold">Textarea</h3>
          <x-field name="sg_textarea" label="Textarea" type="textarea" value="Content" />
        </div>

        <div class="grid content-start gap-4">
          <h3 class="text-lg font-semibold">Choice inputs</h3>
          <x-choice name="sg_check_a">Default checkbox</x-choice>
          <x-choice name="sg_check_b" checked>Default checkbox</x-choice>
          <x-choice name="sg_radio" type="radio" value="a">Default radio</x-choice>
          <x-choice name="sg_radio" type="radio" value="b" checked>Checked radio</x-choice>
        </div>
      </form>
    </section>

    {{-- Formes --}}
    <section class="container-content py-20">
      <h2 class="text-h2">Formes « éclats de verre »</h2>

      <div class="mt-10 grid gap-8 bg-night p-8 sm:grid-cols-2 lg:grid-cols-4">
        @foreach (['a', 'b', 'c', 'd'] as $shape)
          <x-frame :shape="$shape" class="aspect-[4/5]" credit="© 2019 Inspire Metz">
            <div class="frame-media bg-gradient-to-br from-cyan-300 to-dark-blue-700"></div>
          </x-frame>
        @endforeach
      </div>
    </section>

    {{-- Contenu éditorial --}}
    <section class="container-content py-20">
      <div class="prose-inspire max-w-(--container-prose)">
        <h2>Titre de niveau 2</h2>
        <p>Nunc scelerisque tincidunt elit. Vestibulum non mi ipsum. Cras pretium suscipit tellus sit amet aliquet. Pellentesque vehicula est a <a href="#">lien vers page interne</a> lorem gravida bibendum.</p>
        <ul>
          <li>Nec proin est</li>
          <li>Pharetra quis</li>
          <li>Laoreet convallis velit</li>
        </ul>
        <p>Mauris imperdiet orci dapibus, <strong>commodo libero nec</strong>, interdum tortor.</p>
        <ol>
          <li>Nec proin est</li>
          <li>Pharetra quis</li>
          <li>Laoreet convallis velit</li>
        </ol>
        <h3>Titre de niveau 3</h3>
        <blockquote>Fusce at nisi eget dolor rhoncus facilisis. Mauris ante nisl, consectetur et luctus et, porta ut dolor.</blockquote>
      </div>
    </section>
  </div>
@endsection
