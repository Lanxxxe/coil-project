import './bootstrap';
// Hero slideshow for homepage
import './hero';
// Theme manager (light/dark)
import './theme';
// Leaflet-based country/region map (lazy loaded below)
// UX microinteractions and scroll animations
import './ux';
import './card-contrast';
import './header';

// Lazy-load the map code when the map section enters viewport
(() => {
	const el = document.getElementById('leaflet-map');
	if(!el) return;
	const load = () => import(/* webpackChunkName: "map" */ './maplibre-map');
	if('IntersectionObserver' in window){
		const io = new IntersectionObserver(entries => {
			if(entries.some(e => e.isIntersecting)){
				load();
				io.disconnect();
			}
		}, { rootMargin: '200px' });
		io.observe(el);
	} else {
		// Fallback
		setTimeout(load, 0);
	}
})();
