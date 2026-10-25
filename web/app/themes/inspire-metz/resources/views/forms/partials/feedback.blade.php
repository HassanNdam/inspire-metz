{{-- Message global (erreurs, page expirée…) annoncé aux lecteurs d'écran. --}}
<p
  class="text-sm font-semibold text-error"
  role="alert"
  x-cloak
  x-show="message && ! sent"
  x-text="message"
></p>
