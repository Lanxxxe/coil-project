import maplibregl from 'maplibre-gl';
import 'maplibre-gl/dist/maplibre-gl.css';

// Utility to fetch JSON with no-store cache
async function fetchJSON(url) {
  try {
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) return null;
    return await res.json();
  } catch { return null; }
}

document.addEventListener('DOMContentLoaded', () => {
  const mapEl = document.getElementById('leaflet-map'); // reuse same container id
  if (!mapEl) return;

  // Fixed camera mode: show both countries with no zoom/pan interactions
  const FIXED_VIEW = true;

  // Determine basemap style
  // If you have MAPTILER_KEY set on window (injected in Blade), use MapTiler; otherwise basic OSM raster style
  const MT_KEY = window.MAPTILER_KEY || '';
  const urlParams = new URLSearchParams(window.location.search);
  const basemapParam = (urlParams.get('basemap') || urlParams.get('style') || '').toLowerCase();
  const rasterOSMStyle = {
    version: 8,
    sources: {
      osm: {
        type: 'raster',
        tiles: ['https://tile.openstreetmap.org/{z}/{x}/{y}.png'],
        tileSize: 256,
        attribution: '© OpenStreetMap contributors'
      }
    },
    layers: [
      // Background color will be updated after load using CSS variable --map-bg
      { id: 'background', type: 'background', paint: { 'background-color': '#0f0f0f' } },
      { id: 'osm', type: 'raster', source: 'osm' }
    ]
  };

  // Plain white background style with no tiles/labels
  const plainStyle = {
    version: 8,
    sources: {},
    layers: [
      { id: 'background', type: 'background', paint: { 'background-color': '#ffffff' } }
    ]
  };

  // Choose style: default to plain white; allow optional overrides via basemap param
  const chooseStyleKey = (() => {
    if (basemapParam === 'osm') return 'osm';
    if (basemapParam === 'maptiler' || basemapParam === 'mt') return 'mt';
    if (basemapParam === 'plain' || basemapParam === 'none') return 'plain';
    if (window.MAP_PLAIN_BASEMAP === true) return 'plain';
    // default
    return 'plain';
  })();
  const isPlain = chooseStyleKey === 'plain';
  const chosenStyle = isPlain
    ? plainStyle
    : (chooseStyleKey === 'mt' && MT_KEY ? `https://api.maptiler.com/maps/dataviz/style.json?key=${MT_KEY}` : rasterOSMStyle);

  const map = new maplibregl.Map({
    container: mapEl,
    style: chosenStyle,
    center: [122.8, 12.5],
    zoom: 5,
    attributionControl: true,
    dragRotate: false,
    pitchWithRotate: false,
  });

  if (!FIXED_VIEW) {
    map.addControl(new maplibregl.NavigationControl({ showZoom: true, showCompass: false }));
  } else {
    try {
      map.scrollZoom.disable();
      map.boxZoom.disable();
      map.doubleClickZoom.disable();
      map.touchZoomRotate.disable();
      map.keyboard.disable();
      map.dragPan.disable();
    } catch {}
  }

  // Theme-aware color helper
  function cssVar(name, fallback) {
    const v = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
    return v || fallback;
  }
  function themeColors() {
    // Use palette variables
    return {
      fillBase: cssVar('--turquoise', '#26C6DA'),
      fillHover: cssVar('--palm', '#4CAF50'),
      fillActive: cssVar('--sunset', '#FF8A65'),
      // Use text-primary for outline to keep contrast with background in both themes
      lineColor: cssVar('--text-primary', '#ffffff'),
      lineOpacity: 0.6,
    };
  }

  // Side panel references
  const panel = document.getElementById('region-panel');
  const titleEl = panel?.querySelector('[data-panel="title"]');
  const descEl = panel?.querySelector('[data-panel="description"]');
  const countryEl = panel?.querySelector('[data-panel="country"]');
  const metaEl = panel?.querySelector('[data-panel="meta"]');

  const storageKey = 'mapSelection_v2';
  let lastSelectedRegion = '';

  const setQuery = (country, region) => {
    const params = new URLSearchParams(window.location.search);
    params.set('country', country);
    if (region) params.set('region', region); else params.delete('region');
  // ADM1 only: no level parameter
    history.replaceState({}, '', `${location.pathname}?${params.toString()}`);
  };
  const saveSelection = (country, region) => {
    try { localStorage.setItem(storageKey, JSON.stringify({ country, region })); } catch {}
  };

  function updatePanel(country, regionName) {
    if (countryEl) countryEl.textContent = country;
    if (titleEl) titleEl.textContent = regionName || 'Pick a region';
    if (descEl) descEl.textContent = regionName || 'Click a region to populate this panel with its name.';
    if (metaEl) metaEl.textContent = regionName ? `Region: ${regionName}` : '—';
  }

  // Load a low-res world land layer to render other countries in gray (no labels)
  async function loadWorldLand() {
    const candidates = [
      '/data/geo/ne_110m_land.geojson',
      '/data/geo/land_110m.geojson',
      'https://raw.githubusercontent.com/nvkelso/natural-earth-vector/master/geojson/ne_110m_land.geojson',
    ];
    for (const u of candidates) {
      const j = await fetchJSON(u);
      if (j && j.type === 'FeatureCollection' && Array.isArray(j.features) && j.features.length > 0) return j;
    }
    return null;
  }

  function addWorldLandLayer(geojson) {
    if (!geojson) return;
    try {
      if (map.getSource('world-land')) map.removeSource('world-land');
    } catch {}
    map.addSource('world-land', { type: 'geojson', data: geojson });
    // Plain gray land over white background
    if (!map.getLayer('world-land')) {
      map.addLayer({ id: 'world-land', type: 'fill', source: 'world-land', paint: { 'fill-color': '#e5e5e5', 'fill-opacity': 1 } });
    }
  }

  const DATASETS = {
    ph: {
      label: 'Philippines',
      localCandidates: [
        '/data/geo/ph_adm1.geojson', // authoritative dissolved regions
        '/data/geo/ph_regions.min.geojson',
      ],
      remoteCandidates: [
        '/api/geo/adm1?country=ph',
  'https://raw.githubusercontent.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson',
      ],
      view: { center: [121.7740, 12.8797], zoom: 5 },
      id: 'ph',
    },
    id: {
      label: 'Indonesia',
      localCandidates: [
  '/data/geo/id_macro7.geojson',
  '/data/geo/id_adm1.geojson',
  '/data/geo/id_provinces.min.geojson',
      ],
      remoteCandidates: [
        '/api/geo/adm1?country=id',
  'https://raw.githubusercontent.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson',
      ],
      view: { center: [118.0149, -2.5489], zoom: 4 },
      id: 'id',
    },
  };

  // Basic validators so we don't accept broken/placeholder files
  function validateGeo(code, json) {
    if (!json) return false;
    if (json.type === 'FeatureCollection') {
      const n = Array.isArray(json.features) ? json.features.length : 0;
      if (code === 'ph') return n >= 17; // 17 administrative regions
  if (code === 'id') return n >= 6; // accept macro-6/7 or provinces (>30)
      return n > 0;
    }
    return false; // ignore GeometryCollection or others
  }

  async function loadGeo(code) {
    const cfg = DATASETS[code];
    let json = null;
    // Try locals in order, but only accept valid FeatureCollections
    for (const u of (cfg.localCandidates || [])) {
      json = await fetchJSON(u);
      if (validateGeo(code, json)) return json;
    }
    // Then try remotes
    for (const u of (cfg.remoteCandidates || [])) {
      json = await fetchJSON(u);
      if (validateGeo(code, json)) return json;
    }
    return null;
  }

  // For Indonesia grouping (macro regions)
  const normalize = (s='') => s.toString().trim().toLowerCase();
  const ID_GROUP_MAP = {
    'aceh': 'Sumatra', 'north sumatra': 'Sumatra', 'west sumatra': 'Sumatra', 'riau': 'Sumatra', 'riau islands': 'Sumatra', 'jambi': 'Sumatra', 'bengkulu': 'Sumatra', 'south sumatra': 'Sumatra', 'bangka belitung islands': 'Sumatra', 'lampung': 'Sumatra',
    'banten': 'Java','jakarta': 'Java','west java': 'Java','central java': 'Java','yogyakarta': 'Java','east java': 'Java',
    'west kalimantan': 'Kalimantan','central kalimantan': 'Kalimantan','south kalimantan': 'Kalimantan','east kalimantan': 'Kalimantan','north kalimantan': 'Kalimantan',
    'north sulawesi': 'Sulawesi','gorontalo': 'Sulawesi','central sulawesi': 'Sulawesi','west sulawesi': 'Sulawesi','south sulawesi': 'Sulawesi','southeast sulawesi': 'Sulawesi',
    'bali': 'Bali & Nusa Tenggara','west nusa tenggara': 'Bali & Nusa Tenggara','east nusa tenggara': 'Bali & Nusa Tenggara',
    'maluku': 'Maluku','north maluku': 'Maluku',
    'papua': 'Papua','west papua': 'Papua','southwest papua': 'Papua','south papua': 'Papua','central papua': 'Papua','highland papua': 'Papua',
  };

  function featureName(props){
    return props?.name || props?.shapeName || props?.NAME_1 || props?.NAME || props?.region || props?.Province || props?.province || 'Unknown';
  }

  // style layers ids
  const SRC = { ph: 'ph-src', id: 'id-src' };
  const LYR = { ph: 'ph-fill', id: 'id-fill' };
  const OUT = { ph: 'ph-outline', id: 'id-outline' };

  function addCountryLayers(code, geojson) {
    const cfg = DATASETS[code];
    const srcId = SRC[code];
    const fillId = LYR[code];
    const outId = OUT[code];
    if (map.getSource(srcId)) { map.removeLayer(fillId); map.removeLayer(outId); map.removeSource(srcId); }

    // detect a stable id property if present
    const firstProps = geojson?.features?.[0]?.properties || {};
    const idProp = ['shapeID','ID_1','ID','gid','code'].find(k => Object.prototype.hasOwnProperty.call(firstProps, k));

    map.addSource(srcId, { type: 'geojson', data: geojson, generateId: true, ...(idProp ? { promoteId: idProp } : {}) });
    // outline under fill for crisp edges
    const tc = themeColors();
    map.addLayer({
      id: outId, type: 'line', source: srcId,
      paint: { 'line-color': tc.lineColor, 'line-opacity': tc.lineOpacity, 'line-width': 1.2 }
    });
    map.addLayer({
      id: fillId, type: 'fill', source: srcId,
      paint: {
        'fill-color': ['case', ['boolean', ['feature-state','active'], false], tc.fillActive, ['boolean', ['feature-state','hover'], false], tc.fillHover, tc.fillBase],
        'fill-opacity': ['case', ['boolean', ['feature-state','active'], false], 0.85, ['boolean', ['feature-state','hover'], false], 0.65, 0.35]
      }
    });

    // Hover handling (robust per-source)
    map.on('mousemove', fillId, (e) => {
      if (!e.features?.length) return;
      const f = e.features[0];
      const id = (f.id ?? f.properties?.gid ?? f.properties?.ID_1 ?? f.properties?.ID ?? f.properties?.code ?? f.properties?.shapeID);
      const prev = hoveredBySource[srcId];
      if (prev != null && prev !== id) {
        try { map.setFeatureState({ source: srcId, id: prev }, { hover: false }); } catch {}
      }
      hoveredBySource[srcId] = id;
      try { map.setFeatureState({ source: srcId, id }, { hover: true }); } catch {}
      map.getCanvas().style.cursor = 'pointer';
    });
    map.on('mouseleave', fillId, () => {
      map.getCanvas().style.cursor = '';
      const prev = hoveredBySource[srcId];
      if (prev != null) {
        try { map.setFeatureState({ source: srcId, id: prev }, { hover: false }); } catch {}
      }
      hoveredBySource[srcId] = null;
    });

    // Click handling
    map.on('click', fillId, (e) => {
      if (!e.features?.length) return;
      const f = e.features[0];
      selectFeature(code, f);
    });
  }

  // Selection state (using feature-state)
  const activeBySource = { [SRC.ph]: null, [SRC.id]: null };
  // Hover state tracked globally to ensure we can clear on canvas/mouseleave
  const hoveredBySource = { [SRC.ph]: null, [SRC.id]: null };

  function clearActiveStates(exceptSrc = null, exceptId = null) {
    for (const s of Object.values(SRC)) {
      const prev = activeBySource[s];
      if (prev != null && !(s === exceptSrc && prev === exceptId)) {
        try { map.setFeatureState({ source: s, id: prev }, { active: false }); } catch {}
        activeBySource[s] = null;
      }
    }
  }

  // Compute bounds for a GeoJSON feature (Polygon/MultiPolygon/etc.)
  function getFeatureBounds(feature) {
    const geom = feature?.geometry || feature;
    if (!geom) return null;
    let bounds = null;
    const add = (pt) => {
      const lng = +pt[0]; const lat = +pt[1];
      if (!isFinite(lng) || !isFinite(lat)) return;
      if (!bounds) bounds = new maplibregl.LngLatBounds([lng, lat], [lng, lat]);
      else bounds.extend([lng, lat]);
    };
    const walk = (coords) => {
      if (!coords) return;
      if (typeof coords[0] === 'number' && coords.length >= 2) { add(coords); return; }
      for (const c of coords) walk(c);
    };
    if (geom.type === 'Point') add(geom.coordinates);
    else walk(geom.coordinates);
    return bounds;
  }

  // Compute bounds for a FeatureCollection by union of feature bounds
  function getCollectionBounds(fc) {
    if (!fc || !Array.isArray(fc.features)) return null;
    let bounds = null;
    for (const f of fc.features) {
      const b = getFeatureBounds(f);
      if (!b) continue;
      if (!bounds) bounds = new maplibregl.LngLatBounds(b.getSouthWest(), b.getNorthEast());
      else bounds.extend(b);
    }
    return bounds;
  }

  // Padding so selected regions aren’t obscured by the overlay panel
  function getMapPadding() {
    const base = 16;
    const isDesktop = window.matchMedia('(min-width: 768px)').matches;
    if (isDesktop && panel && mapEl) {
      const rect = panel.getBoundingClientRect();
      const cont = mapEl.getBoundingClientRect();
      const top = Math.min(Math.round((rect.height || 200) + 12), Math.round(cont.height * 0.45));
      const left = Math.min(Math.round((rect.width || 320) + 12), Math.round(cont.width * 0.45));
      return { top, left, right: base, bottom: base };
    }
    return { top: base, left: base, right: base, bottom: base };
  }

  // Fixed framing padding: flush to top/left, small right/bottom margin
  function getFixedPadding() { return { top: 0, left: 0, right: 12, bottom: 12 }; }

  function selectFeature(code, feature) {
    const srcId = SRC[code];
    const fillId = LYR[code];
    const props = feature.properties || {};
    const name = featureName(props);

  // Turn off previously active on ALL sources
  clearActiveStates();

    const id = feature.id ?? props.gid ?? props.ID_1 ?? props.ID ?? props.code ?? name;
    activeBySource[srcId] = id;
    map.setFeatureState({ source: srcId, id }, { active: true });

    lastSelectedRegion = name;
    updatePanel(DATASETS[code].label, name);
    saveSelection(code, name);
    setQuery(code, name);

    // Keep camera static in fixed mode; otherwise zoom to selection
    if (!FIXED_VIEW) {
      try {
        const b = getFeatureBounds(feature);
        if (b) {
          map.fitBounds(b, { padding: getMapPadding(), duration: 700, maxZoom: code === 'ph' ? 7.5 : 6.5 });
        }
      } catch {}
    }
  }

  // Try to find and select a feature by its display name (used for deep-link/persisted selection)
  function trySelectByName(code, rawName) {
    if (!rawName) return false;
    const srcId = SRC[code];
    try {
      // Ensure source exists
      if (!map.getSource(srcId)) return false;
      const target = normalize(rawName);
      // For some common aliases (mainly Indonesia macro group variants)
      const ALIASES = {
        id: {
          'nusa tenggara': 'bali',
          'bali & nusa tenggara': 'bali',
          'kalimantan': 'kalimantan',
          'sulawesi': 'sulawesi',
          'papua': 'papua',
          'sumatra': 'sumatra',
          'java': 'java',
          'jakarta': 'jakarta',
        },
        ph: {}
      };

      const alias = (ALIASES[code] && ALIASES[code][target]) ? ALIASES[code][target] : target;
      const feats = map.querySourceFeatures(srcId) || [];
      for (const f of feats) {
        const props = f.properties || {};
        const name = featureName(props);
        const cand = normalize(name);
        if (cand === alias || normalize(props.NAME_1 || '') === alias || normalize(props.region || '') === alias || normalize(props.province || props.Province || '') === alias) {
          selectFeature(code, f);
          return true;
        }
      }
    } catch {}
    return false;
  }

  map.on('load', async () => {
    // Sync background with theme
  try { map.setPaintProperty('background', 'background-color', isPlain ? '#ffffff' : cssVar('--map-bg', '#0f0f0f')); } catch {}

    // Parse query/persisted selection
    const params = new URLSearchParams(location.search);
    const qCountry = (params.get('country') || '').toLowerCase();
    const qRegion = params.get('region') || '';
    const persisted = (() => { try { return JSON.parse(localStorage.getItem(storageKey)); } catch { return null; } })();

    // When using the plain style, add a gray world land base (no labels)
    let land = null;
    if (isPlain) {
      try { land = await loadWorldLand(); addWorldLandLayer(land); } catch {}
    }

  // Load both countries concurrently (render above land base if present)
  const [geoPH, geoID] = await Promise.all([loadGeo('ph'), loadGeo('id')]);
    if (geoPH) addCountryLayers('ph', geoPH);
    if (geoID) addCountryLayers('id', geoID);

    // Initial neutral panel
    updatePanel('—', '');

    // Respect deep-link camera only when not fixed
    if (!FIXED_VIEW && ['ph','id'].includes(qCountry)) {
      const v = DATASETS[qCountry].view;
      map.easeTo({ center: v.center, zoom: v.zoom, duration: 600, padding: getMapPadding() });
    }

    // Deep-link or persisted selection
    if (qRegion) {
      // Prefer url-specified country if valid
      if (['ph','id'].includes(qCountry) && trySelectByName(qCountry, qRegion)) {
        // done
      } else {
        // Try PH first, then ID for ambiguous names
        if (!trySelectByName('ph', qRegion)) {
          trySelectByName('id', qRegion);
        }
      }
    } else if (persisted?.region) {
      const prefCountry = ['ph','id'].includes(persisted.country) ? persisted.country : 'ph';
      if (!trySelectByName(prefCountry, persisted.region)) {
        // fall back to the other
        const other = prefCountry === 'ph' ? 'id' : 'ph';
        trySelectByName(other, persisted.region);
      }
    }

    // Fixed view: fit to both PH and ID and lock zoom
    if (FIXED_VIEW) {
      try {
        let both = null;
        const bPH = geoPH ? getCollectionBounds(geoPH) : null;
        const bID = geoID ? getCollectionBounds(geoID) : null;
        if (bPH) both = new maplibregl.LngLatBounds(bPH.getSouthWest(), bPH.getNorthEast());
        if (bID) both = both ? both.extend(bID) : new maplibregl.LngLatBounds(bID.getSouthWest(), bID.getNorthEast());
        if (both) {
          map.fitBounds(both, { padding: getFixedPadding(), duration: 0 });
          const z = map.getZoom();
          map.setMinZoom(z);
          map.setMaxZoom(z);
        }
      } catch {}
    }

    // React to theme changes
    window.addEventListener('themechange', () => {
      const { lineColor, lineOpacity, fillBase, fillHover, fillActive } = themeColors();
      // background color
  try { map.setPaintProperty('background', 'background-color', isPlain ? '#ffffff' : cssVar('--map-bg', '#0f0f0f')); } catch {}
      for (const code of ['ph','id']) {
        const outId = OUT[code];
        const fillId = LYR[code];
        if (map.getLayer(outId)) {
          map.setPaintProperty(outId, 'line-color', lineColor);
          map.setPaintProperty(outId, 'line-opacity', lineOpacity);
        }
        if (map.getLayer(fillId)) {
          map.setPaintProperty(fillId, 'fill-color', ['case', ['boolean', ['feature-state','active'], false], fillActive, ['boolean', ['feature-state','hover'], false], fillHover, fillBase]);
        }
      }
    });

    // Clear hover if mouse leaves the canvas entirely
    try {
      map.getCanvas().addEventListener('mouseleave', () => {
        for (const s of Object.values(SRC)) {
          const prev = hoveredBySource[s];
          if (prev != null) {
            try { map.setFeatureState({ source: s, id: prev }, { hover: false }); } catch {}
            hoveredBySource[s] = null;
          }
        }
        map.getCanvas().style.cursor = '';
      });
    } catch {}

    // On resize, re-fit both countries when fixed; otherwise adjust padding
    window.addEventListener('resize', () => {
      try {
        if (FIXED_VIEW) {
          let both = null;
          const bPH = geoPH ? getCollectionBounds(geoPH) : null;
          const bID = geoID ? getCollectionBounds(geoID) : null;
          if (bPH) both = new maplibregl.LngLatBounds(bPH.getSouthWest(), bPH.getNorthEast());
          if (bID) both = both ? both.extend(bID) : new maplibregl.LngLatBounds(bID.getSouthWest(), bID.getNorthEast());
          if (both) map.fitBounds(both, { padding: getFixedPadding(), duration: 0 });
        } else {
          map.easeTo({ padding: getMapPadding(), duration: 0 });
        }
      } catch {}
    });
  });
});
