/**
 * Formulaires du mu-plugin inspire-metz-core, envoyés en fetch().
 * Sans JavaScript, le même formulaire poste normalement et le serveur redirige.
 */
export default (initial = {}) => ({
  sent: initial.sent ?? false,
  message: initial.message ?? null,
  errors: initial.errors ?? {},
  loading: false,
  step: 1,

  init() {
    this.goToFirstError();
  },

  clear(key) {
    if (this.errors[key]) {
      const { [key]: _removed, ...rest } = this.errors;
      this.errors = rest;
    }
  },

  /**
   * Étape suivante (formulaire ambassadeur) si les champs visibles sont valides.
   */
  next() {
    const step = this.$root.querySelector(`[data-step="${this.step}"]`);
    const invalid = [...step.querySelectorAll('input, select, textarea')].find((field) => !field.checkValidity());

    if (invalid) {
      invalid.reportValidity();
      return;
    }

    this.step += 1;
    this.$nextTick(() => this.$root.querySelector(`[data-step="${this.step}"] input`)?.focus());
  },

  async submit() {
    if (this.loading) {
      return;
    }

    this.loading = true;
    this.message = null;

    try {
      // getAttribute : le champ caché name="action" masque la propriété form.action.
      const response = await fetch(this.$root.getAttribute('action'), {
        method: 'POST',
        body: new FormData(this.$root),
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      });

      const data = await response.json();

      this.message = data.message;
      this.errors = data.errors ?? {};

      if (data.success) {
        this.sent = true;
        this.$root.reset();
        this.$dispatch('im-form-sent');
      } else {
        this.$nextTick(() => this.goToFirstError());
      }
    } catch {
      this.message = 'Une erreur est survenue. Merci de réessayer dans quelques instants.';
    } finally {
      this.loading = false;
    }
  },

  goToFirstError() {
    const first = Object.keys(this.errors)[0];

    if (!first) {
      return;
    }

    const field = this.$root.querySelector(`[name="im[${first}]"]`);
    const step = field?.closest('[data-step]');

    if (step) {
      this.step = Number(step.dataset.step);
    }

    this.$nextTick(() => field?.focus());
  },
});
