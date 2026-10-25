/**
 * État global des surcouches : menu, newsletter, mission, vidéo.
 * Une seule surcouche ouverte à la fois.
 */
export default {
  menu: false,
  newsletter: false,
  mission: null,
  video: null,

  toggleMenu() {
    this.menu = !this.menu;
  },

  openMission(id) {
    this.menu = false;
    this.mission = id;
  },

  /**
   * Convertit une URL YouTube ou Vimeo en URL d'intégration avec lecture automatique.
   */
  playVideo(url) {
    const youtube = url.match(/(?:youtu\.be\/|v=|embed\/|shorts\/)([\w-]{11})/);
    const vimeo = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);

    if (youtube) {
      this.video = `https://www.youtube-nocookie.com/embed/${youtube[1]}?autoplay=1&rel=0`;
    } else if (vimeo) {
      this.video = `https://player.vimeo.com/video/${vimeo[1]}?autoplay=1`;
    } else {
      window.open(url, '_blank', 'noopener');
    }
  },
};
