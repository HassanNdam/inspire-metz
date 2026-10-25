import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';

import ui from './stores/ui';
import siteHeader from './components/site-header';
import imForm from './components/im-form';
import carousel from './components/carousel';
import slider from './components/slider';
import parallax from './components/parallax';
import heroScroll from './components/hero-scroll';
import loadMore from './components/load-more';

Alpine.plugin(collapse);
Alpine.plugin(focus);

Alpine.store('ui', ui);

Alpine.data('siteHeader', siteHeader);
Alpine.data('imForm', imForm);
Alpine.data('carousel', carousel);
Alpine.data('slider', slider);
Alpine.data('parallax', parallax);
Alpine.data('heroScroll', heroScroll);
Alpine.data('loadMore', loadMore);

window.Alpine = Alpine;

Alpine.start();
