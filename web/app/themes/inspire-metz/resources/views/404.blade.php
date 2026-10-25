@extends('layouts.app')

@section('content')
  <section class="pb-32 pt-36 text-center md:pt-44">
    <div class="container-content">
      <p class="error-code" aria-hidden="true">4<span class="error-code-zero">0</span>4</p>
      <h1 class="mt-10 text-h2">{{ __('Vous êtes perdu ?', 'inspire-metz') }}</h1>
      <p class="mx-auto mt-6 max-w-xl text-lead">
        {{ __('La page que vous cherchez n’existe pas ou a été déplacée. Revenez à l’accueil pour poursuivre votre découverte de l’Euro-Métropole de Metz.', 'inspire-metz') }}
      </p>
      <x-button :href="home_url('/')" variant="tertiary" size="sm" class="mt-10">{{ __('Retour', 'inspire-metz') }}</x-button>
    </div>
  </section>
@endsection
