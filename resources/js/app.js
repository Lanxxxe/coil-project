import './bootstrap';
// Theme manager (light/dark)
import './theme';
// UX microinteractions and scroll animations (lightweight)
import './ux';
import './card-contrast';

// Lazy-load heavier features to reduce initial bundle
function onReady(fn) {
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn, { once: true });
    else fn();
}

onReady(() => {
    // Load hero slideshow only when the hero is present
    if (document.getElementById('hero-carousel')) {
        import(/* webpackChunkName: "hero" */ './hero');
    }
    // Defer MapLibre map loading until map section is near viewport
    const mapEl = document.getElementById('leaflet-map');
    if (mapEl) {
        const loadMap = () => import(/* webpackChunkName: "map" */ './maplibre-map');
        try {
            if ('IntersectionObserver' in window) {
                const io = new IntersectionObserver((entries, obs) => {
                    for (const e of entries) {
                        if (e.isIntersecting) {
                            obs.disconnect();
                            loadMap();
                            break;
                        }
                    }
                }, { root: null, threshold: 0, rootMargin: '200px' });
                io.observe(mapEl);
            } else {
                // Fallback: no IO support
                loadMap();
            }
        } catch {
            // Safety fallback
            loadMap();
        }
    }
});
