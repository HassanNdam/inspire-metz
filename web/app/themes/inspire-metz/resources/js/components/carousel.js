/**
 * Rangée défilante (actualités de l'accueil) : défilement natif + scroll-snap,
 * boutons précédent / suivant et glisser à la souris.
 */
export default () => ({
  atStart: true,
  atEnd: false,

  init() {
    const track = this.$refs.track;

    this.update = () => {
      this.atStart = track.scrollLeft <= 4;
      this.atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
    };

    track.addEventListener('scroll', this.update, { passive: true });
    window.addEventListener('resize', this.update);
    this.update();

    let startX = 0;
    let startScroll = 0;
    let dragging = false;

    track.addEventListener('pointerdown', (event) => {
      if (event.pointerType !== 'mouse') {
        return;
      }

      dragging = true;
      startX = event.clientX;
      startScroll = track.scrollLeft;
    });

    window.addEventListener('pointermove', (event) => {
      if (!dragging) {
        return;
      }

      if (Math.abs(event.clientX - startX) > 4) {
        track.classList.add('is-dragging');
      }

      track.scrollLeft = startScroll - (event.clientX - startX);
    });

    window.addEventListener('pointerup', () => {
      dragging = false;
      setTimeout(() => track.classList.remove('is-dragging'));
    });

    // Un glisser ne doit pas ouvrir la carte sous le curseur.
    track.addEventListener('click', (event) => {
      if (track.classList.contains('is-dragging')) {
        event.preventDefault();
      }
    }, true);
  },

  scroll(direction) {
    const track = this.$refs.track;
    const card = track.firstElementChild;
    const step = card ? card.getBoundingClientRect().width + 24 : track.clientWidth * 0.8;

    track.scrollBy({ left: direction * step, behavior: 'smooth' });
  },
});
