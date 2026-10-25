/**
 * Diaporama simple (bloc « Galerie ») : une image à la fois, flèches et puces.
 */
export default (count = 0) => ({
  index: 0,
  count,

  prev() {
    this.index = (this.index - 1 + this.count) % this.count;
  },

  next() {
    this.index = (this.index + 1) % this.count;
  },

  go(index) {
    this.index = index;
  },
});
