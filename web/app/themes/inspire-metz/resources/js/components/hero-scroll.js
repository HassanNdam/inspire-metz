/**
 * Hero de l'accueil (desktop) : pose --p (0 → 1) selon le défilement dans la
 * section. L'éclat vidéo s'agrandit sur 110vh de défilement, comme sur le site
 * en ligne, puis le contenu territoire apparaît.
 */
export default () => ({
  revealed: false,

  init() {
    const desktop = window.matchMedia('(min-width: 64rem)');
    let frame = null;

    const update = () => {
      frame = null;

      if (!desktop.matches) {
        this.$el.style.removeProperty('--p');
        this.revealed = true;
        return;
      }

      const top = this.$el.getBoundingClientRect().top + window.scrollY;
      const progress = Math.min(1, Math.max(0, (window.scrollY - top) / (window.innerHeight * 1.1)));

      this.$el.style.setProperty('--p', progress.toFixed(4));
      this.revealed = progress >= 0.98;
    };

    const schedule = () => {
      frame ??= requestAnimationFrame(update);
    };

    update();
    window.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule);
    desktop.addEventListener('change', schedule);
  },
});
