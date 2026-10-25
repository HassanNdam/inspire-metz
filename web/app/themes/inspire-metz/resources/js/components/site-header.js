/**
 * En-tête : fond plein dès qu'on a défilé, masqué en descendant,
 * réaffiché en remontant (maquette p. 5).
 */
export default () => ({
  hidden: false,
  solid: false,
  lastY: 0,

  init() {
    const update = () => {
      const y = window.scrollY;

      this.solid = y > 40;
      this.hidden = y > 200 && y > this.lastY;
      this.lastY = y;
    };

    update();
    window.addEventListener('scroll', update, { passive: true });
  },
});
