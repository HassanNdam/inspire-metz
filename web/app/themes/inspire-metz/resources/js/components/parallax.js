/**
 * Hero de l'accueil : les éclats suivent le curseur (« Déplacer votre curseur
 * pour explorer »). Expose --mx / --my entre -1 et 1 sur l'élément ; le
 * mouvement est écouté sur la section parente (l'élément peut être transparent
 * aux pointeurs).
 */
export default () => ({
  init() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return;
    }

    let frame = null;
    const area = this.$el.closest('section') ?? this.$el;

    area.addEventListener('pointermove', (event) => {
      if (event.pointerType !== 'mouse' || frame) {
        return;
      }

      frame = requestAnimationFrame(() => {
        this.$el.style.setProperty('--mx', ((event.clientX / window.innerWidth) * 2 - 1).toFixed(3));
        this.$el.style.setProperty('--my', ((event.clientY / window.innerHeight) * 2 - 1).toFixed(3));
        frame = null;
      });
    });

    area.addEventListener('pointerleave', () => {
      this.$el.style.setProperty('--mx', 0);
      this.$el.style.setProperty('--my', 0);
    });
  },
});
