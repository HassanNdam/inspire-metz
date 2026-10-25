{{-- Candidature au Club des Ambassadeurs en deux étapes (maquette p. 16-17). --}}
@php
  use InspireMetz\Core\Forms\Forms;
  use InspireMetz\Core\Forms\Handler;

  $state = Handler::state('ambassadeur');
  $old = $state['old'];
@endphp

<form
  id="form-ambassadeur"
  method="post"
  action="{{ admin_url('admin-post.php') }}"
  class="grid gap-5"
  x-data="imForm(@js($state))"
  x-on:submit.prevent="submit"
  novalidate
>
  {!! Handler::hidden('ambassadeur') !!}

  <template x-if="sent">
    @include('forms.partials.success', ['tone' => 'light'])
  </template>

  <fieldset x-show="! sent && step === 1" data-step="1" class="grid gap-5">
    <legend class="mb-5 text-h3 text-ink">{{ __('Vos informations', 'inspire-metz') }}</legend>

    <div class="flex gap-6" role="radiogroup" aria-label="{{ __('Civilité', 'inspire-metz') }}">
      <x-choice name="im[civilite]" type="radio" value="madame" :checked="($old['civilite'] ?? 'madame') === 'madame'" live>{{ __('Madame', 'inspire-metz') }}</x-choice>
      <x-choice name="im[civilite]" type="radio" value="monsieur" :checked="($old['civilite'] ?? '') === 'monsieur'" live>{{ __('Monsieur', 'inspire-metz') }}</x-choice>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
      <x-field name="im[nom]" :label="__('Nom', 'inspire-metz')" :value="$old['nom'] ?? ''" autocomplete="family-name" required live />
      <x-field name="im[prenom]" :label="__('Prénom', 'inspire-metz')" :value="$old['prenom'] ?? ''" autocomplete="given-name" required live />
    </div>

    <x-field name="im[societe]" :label="__('Société / Organisme', 'inspire-metz')" :value="$old['societe'] ?? ''" autocomplete="organization" required live />

    <div class="grid gap-5 sm:grid-cols-2">
      <x-field name="im[secteur]" type="select" :label="__('Secteur d’activité', 'inspire-metz')" :options="Forms::sectors()" :value="$old['secteur'] ?? ''" required live />
      <x-field name="im[fonction]" :label="__('Fonction', 'inspire-metz')" :value="$old['fonction'] ?? ''" autocomplete="organization-title" required live />
    </div>

    <x-field name="im[email]" type="email" :label="__('Adresse mail', 'inspire-metz')" :value="$old['email'] ?? ''" autocomplete="email" required live />

    <div>
      <x-button variant="secondary" x-on:click="next">{{ __('Suivant', 'inspire-metz') }}</x-button>
    </div>
  </fieldset>

  <fieldset x-cloak x-show="! sent && step === 2" data-step="2" class="grid gap-5">
    <legend class="mb-5 text-h3 text-ink">{{ __('Vos coordonnées', 'inspire-metz') }}</legend>

    <x-field name="im[adresse]" :label="__('N° et rue', 'inspire-metz')" :value="$old['adresse'] ?? ''" autocomplete="street-address" required live />

    <div class="grid gap-5 sm:grid-cols-2">
      <x-field name="im[code_postal]" :label="__('Code postal', 'inspire-metz')" :value="$old['code_postal'] ?? ''" autocomplete="postal-code" required live />
      <x-field name="im[ville]" :label="__('Ville', 'inspire-metz')" :value="$old['ville'] ?? ''" autocomplete="address-level2" required live />
    </div>

    <x-field name="im[pays]" :label="__('Pays', 'inspire-metz')" :value="$old['pays'] ?? 'France'" autocomplete="country-name" required live />

    <div class="grid gap-5 sm:grid-cols-2">
      <x-field name="im[telephone_fixe]" type="tel" :label="__('Téléphone fixe', 'inspire-metz')" :value="$old['telephone_fixe'] ?? ''" optional live />
      <x-field name="im[telephone_portable]" type="tel" :label="__('Téléphone portable', 'inspire-metz')" :value="$old['telephone_portable'] ?? ''" autocomplete="tel" required live />
    </div>

    <div class="grid gap-3 text-sm">
      <x-choice name="im[newsletter_club]" :checked="! empty($old['newsletter_club'])" live>{{ __('Je souhaite recevoir la newsletter du Club des Ambassadeurs', 'inspire-metz') }}</x-choice>
      <x-choice name="im[infos_partenaires]" :checked="! empty($old['infos_partenaires'])" live>{{ __('Je souhaite recevoir des informations de l’agence Inspire Metz - Office de Tourisme et de ses partenaires', 'inspire-metz') }}</x-choice>
      <x-choice name="im[consentement]" :checked="! empty($old['consentement'])" required live>
        {!! str_replace(':link', '<a href="'.esc_url(get_privacy_policy_url() ?: '#').'" class="underline">'.esc_html__('confidentialité', 'inspire-metz').'</a>', esc_html__('J’accepte que l’agence Inspire Metz utilise les informations transmises dans le cadre de ma demande. En savoir plus sur la page :link.', 'inspire-metz')) !!}
      </x-choice>
    </div>

    @include('forms.partials.feedback')

    <div class="flex flex-wrap gap-3">
      <x-button type="submit" x-bind:disabled="loading">{{ __('Envoyer', 'inspire-metz') }}</x-button>
      <x-button variant="tertiary" icon="arrow-left" x-on:click="step = 1">{{ __('Retour', 'inspire-metz') }}</x-button>
    </div>
  </fieldset>
</form>
