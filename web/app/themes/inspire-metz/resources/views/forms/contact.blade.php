@php
  use InspireMetz\Core\Forms\Handler;

  $state = Handler::state('contact');
  $old = $state['old'];
@endphp

<form
  id="form-contact"
  method="post"
  action="{{ admin_url('admin-post.php') }}"
  class="grid gap-5"
  x-data="imForm(@js($state))"
  x-on:submit.prevent="submit"
  novalidate
>
  {!! Handler::hidden('contact') !!}

  <template x-if="sent">
    @include('forms.partials.success', ['tone' => 'light'])
  </template>

  <div x-show="! sent" class="grid gap-5">
    <x-field name="im[nom]" :label="__('Nom', 'inspire-metz')" :value="$old['nom'] ?? ''" autocomplete="name" required live />
    <x-field name="im[email]" type="email" :label="__('Adresse email', 'inspire-metz')" :value="$old['email'] ?? ''" autocomplete="email" required live />
    <x-field name="im[telephone]" type="tel" :label="__('Téléphone fixe', 'inspire-metz')" :value="$old['telephone'] ?? ''" autocomplete="tel" optional live />
    <x-field name="im[message]" type="textarea" :label="__('Message', 'inspire-metz')" :value="$old['message'] ?? ''" required live />

    @include('forms.partials.legal', ['text' => __('En envoyant ce formulaire vous acceptez l’utilisation des données transmises dans le cadre du traitement de votre demande. Pour en savoir plus, consultez notre page :link.', 'inspire-metz')])

    @include('forms.partials.feedback')

    <div>
      <x-button type="submit" x-bind:disabled="loading">{{ __('Envoyer', 'inspire-metz') }}</x-button>
    </div>
  </div>
</form>
