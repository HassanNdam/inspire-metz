/**
 * « Charger plus » : récupère la page suivante de la liste et ajoute ses
 * éléments. Le bouton est un vrai lien de pagination (fonctionne sans JS).
 */
export default (next = null) => ({
  next,
  loading: false,

  async load() {
    if (!this.next || this.loading) {
      return;
    }

    this.loading = true;

    try {
      const html = await (await fetch(this.next, { credentials: 'same-origin' })).text();
      const page = new DOMParser().parseFromString(html, 'text/html');
      const id = this.$el.dataset.list;
      const items = page.querySelector(`[data-list="${id}"] [data-items]`);
      const firstNew = items?.firstElementChild;

      this.$refs.items.append(...(items ? items.children : []));
      this.next = page.querySelector(`[data-list="${id}"]`)?.dataset.next || null;

      firstNew?.querySelector('a')?.focus({ preventScroll: true });
    } finally {
      this.loading = false;
    }
  },
});
