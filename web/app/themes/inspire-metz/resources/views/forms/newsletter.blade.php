@php
  use InspireMetz\Core\Forms\Handler;

  $state = Handler::state('newsletter');
  $old = $state['old'];
  $context = $context ?? 'page';
@endphp

<form
  id="form-newsletter{{ $context === 'modal' ? '-modal' : '' }}"
  method="post"
  action="{{ admin_url('admin-post.php') }}"
  class="grid gap-4"
  x-data="imForm(@js($state))"
  x-on:submit.prevent="submit"
  novalidate
>
  {!! Handler::hidden('newsletter') !!}

  <template x-if="sent">
    @include('forms.partials.success', [
      'tone' => 'light',
      'title' => __('Votre abonnement a bien été pris en compte !', 'inspire-metz'),
      'text' => __('Vous recevrez désormais toutes les actualités de l’agence Inspire Metz.', 'inspire-metz'),
      'back' => $context === 'modal',
    ])
  </template>

  <div x-show="! sent" class="grid gap-4">
    <x-field name="im[nom]" :label="__('Nom', 'inspire-metz')" :value="$old['nom'] ?? ''" autocomplete="family-name" required live />
    <x-field name="im[prenom]" :label="__('Prénom', 'inspire-metz')" :value="$old['prenom'] ?? ''" autocomplete="given-name" required live />
    <x-field name="im[code_postal]" :label="__('Code postal', 'inspire-metz')" :value="$old['code_postal'] ?? ''" autocomplete="postal-code" optional live />
    <x-field name="im[email]" type="email" :label="__('Adresse mail', 'inspire-metz')" :value="$old['email'] ?? ''" autocomplete="email" required live />

    @include('forms.partials.legal', ['text' => __('En envoyant ce formulaire vous souscrivez à la newsletter d’Inspire Metz. Vous pouvez changer d’avis à tout moment en cliquant sur le lien « Se désinscrire » situé dans chaque e-mail que vous recevrez de notre part. Pour en savoir plus, consultez notre page :link.', 'inspire-metz')])

    @include('forms.partials.feedback')

    <div>
      <x-button type="submit" x-bind:disabled="loading">{{ __('S’inscrire', 'inspire-metz') }}</x-button>
    </div>
  </div>
</form>
