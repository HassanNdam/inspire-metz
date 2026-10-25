{{-- Confirmation d'envoi (maquette p. 22). Rendu dans un <template x-if>. --}}
<div class="grid justify-items-center gap-4 py-10 text-center" role="status">
  <div class="relative mb-2 size-20">
    <span class="grid size-16 place-items-center rounded-full border-2 border-mission-visiter text-mission-visiter">
      <x-icon name="check" class="size-8" />
    </span>
    <img src="{{ Vite::asset('resources/images/brand/paper-plane.svg') }}" alt="" class="absolute -right-6 top-2 w-16">
  </div>

  @if (! empty($title))
    <p class="text-h3 text-ink">{{ $title }}</p>
    <p class="max-w-sm text-slate-700">{{ $text }}</p>
  @else
    <p class="max-w-sm text-lead text-ink" x-text="message"></p>
  @endif

  @if (! empty($back))
    <x-button variant="tertiary" x-on:click="$store.ui.newsletter = false">{{ __('Retour au site', 'inspire-metz') }}</x-button>
  @endif
</div>
