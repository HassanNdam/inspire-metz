{{-- Inscription newsletter en panneau latéral (maquette p. 11). --}}
<div
  x-data
  x-cloak
  x-show="$store.ui.newsletter"
  x-trap.inert.noscroll="$store.ui.newsletter"
  @keydown.escape.window="$store.ui.newsletter = false"
  class="fixed inset-0 z-50"
  role="dialog"
  aria-modal="true"
  aria-labelledby="newsletter-modal-title"
>
  <div
    x-show="$store.ui.newsletter"
    x-transition.opacity.duration.300ms
    class="absolute inset-0 bg-dark-blue-950/70 backdrop-blur-xl"
    @click="$store.ui.newsletter = false"
  ></div>

  <div
    x-show="$store.ui.newsletter"
    x-transition:enter="transition duration-500 ease-out-soft"
    x-transition:enter-start="translate-x-full"
    x-transition:leave="transition duration-300 ease-in"
    x-transition:leave-end="translate-x-full"
    class="modal-panel absolute inset-y-0 right-0 w-full max-w-xl overflow-y-auto bg-slate-100 bg-grid"
  >
    <div class="px-6 pb-12 pt-16 sm:px-12">
      <button type="button" class="btn-icon absolute right-5 top-5 bg-slate-700! hover:bg-slate-800!" @click="$store.ui.newsletter = false">
        <x-icon name="close" label="{{ __('Fermer', 'inspire-metz') }}" />
      </button>

      <h2 id="newsletter-modal-title" class="mb-8 text-h3 font-medium">{{ __('S’inscrire à la newsletter Inspire Metz', 'inspire-metz') }}</h2>

      @include('forms.newsletter', ['context' => 'modal'])
    </div>
  </div>
</div>
