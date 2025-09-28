import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

document.addEventListener('DOMContentLoaded', () => {
  const mapEl = document.getElementById('leaflet-map');
  if (!mapEl) return;

  const map = L.map(mapEl, {
    zoomControl: true,
    attributionControl: true,
    scrollWheelZoom: true,
    minZoom: 3,
    maxZoom: 18,
  }).setView([12.5, 122.8], 5);

  // Add OpenStreetMap basemap for real-world context
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors',
  }).addTo(map);

  // Create a pane for regions above tiles
  const regionsPane = map.createPane('regions-pane');
  regionsPane.style.zIndex = 450;
  regionsPane.style.pointerEvents = 'auto';

  // Base solid style (we're not using tile layers for now)
  const baseStyle = {
  color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim() || 'rgba(255,255,255,0.6)',
    weight: 2,
  fillColor: getComputedStyle(document.documentElement).getPropertyValue('--turquoise').trim() || 'rgba(147, 197, 253, 0.25)', // theme turquoise
    fillOpacity: 1,
  };
  const hoverStyle = { fillColor: getComputedStyle(document.documentElement).getPropertyValue('--palm').trim() || 'rgba(59,130,246,0.55)' };
  const activeStyle = { fillColor: getComputedStyle(document.documentElement).getPropertyValue('--sunset').trim() || '#e11d48', color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary').trim() || '#ffffff', weight: 2 };

  // Left panel elements
  const panel = document.getElementById('region-panel');
  const titleEl = panel?.querySelector('[data-panel="title"]');
  const descEl = panel?.querySelector('[data-panel="description"]');
  const countryEl = panel?.querySelector('[data-panel="country"]');
  const metaEl = panel?.querySelector('[data-panel="meta"]');

  let activeLayer = null;
  let currentCountry = 'ph';
  let phGeoLayer = null;
  let idGeoLayer = null;
  let geoCache = { ph: null, id: null };
  let lastSelectedRegion = null;
  let currentLevel = 'adm1';

  // Helpers for URL and storage
  const storageKey = 'mapSelection_v2';
  const saveSelection = (country, region) => {
    try { localStorage.setItem(storageKey, JSON.stringify({ country, region, level: currentLevel })); } catch {}
  };
  const readSelection = () => {
    try {
      const raw = localStorage.getItem(storageKey);
      return raw ? JSON.parse(raw) : null;
    } catch { return null; }
  };
  const setQuery = (country, region) => {
    const params = new URLSearchParams(window.location.search);
    params.set('country', country);
    if (region) params.set('region', region); else params.delete('region');
    params.set('level', currentLevel);
    const url = `${window.location.pathname}?${params.toString()}`;
    window.history.replaceState({}, '', url);
  };

  function updatePanel(country, regionName, places = null) {
    if (countryEl) countryEl.textContent = country;
    if (titleEl) titleEl.textContent = regionName || 'Pick a region';
    if (descEl) {
      if (!regionName) {
        descEl.textContent = 'Click a region to populate this panel with its name.';
      } else if (Array.isArray(places) && places.length) {
        // Render top N place names
        const names = places.map(p => p.name).join(' • ');
        descEl.textContent = names;
      } else {
        descEl.textContent = regionName;
      }
    }
    if (metaEl) metaEl.textContent = regionName ? `Region: ${regionName}` : '—';
  }

  // (old onSelectRegion removed; new version defined later)

  function wireLayer(layer, country, regionName) {
    layer.on('click keypress', (e) => {
      if (e.type === 'keypress' && !['Enter', ' '].includes(e.originalEvent.key)) return;
      onSelectRegion(country, regionName, layer);
    });
    layer.on('mouseover', () => {
      layer.setStyle(hoverStyle);
      if (layer.bringToFront) layer.bringToFront();
    });
    layer.on('mouseout', () => {
      if (currentCountry === 'id' && lastSelectedRegion === regionName) {
        // keep group highlighted for Indonesia selection
        layer.setStyle(activeStyle);
      } else if (activeLayer === layer) layer.setStyle(activeStyle);
      else layer.setStyle(baseStyle);
    });
  const path = typeof layer.getElement === 'function' ? layer.getElement() : null;
    if (path) {
      path.setAttribute('tabindex', '0');
      path.setAttribute('role', 'button');
      path.setAttribute('aria-label', `${regionName}, ${country}`);
    }
  }

  // Load GeoJSON helpers
  function buildGeoLayer(geojson, countryLabel) {
    const getFeatureName = (props) => {
      return (
        props?.name ||
        props?.shapeName ||
        props?.NAME_1 ||
        props?.NAME ||
        props?.region ||
        props?.Province ||
        props?.province ||
        'Unknown'
      );
    };
    const layer = L.geoJSON(geojson, {
      style: () => baseStyle,
      pane: 'regions-pane',
      onEachFeature: (feature, lyr) => {
        const name = getFeatureName(feature.properties || {});
        lyr.options.interactive = true;
        wireLayer(lyr, countryLabel, name);
      },
    });
    return layer;
  }

  // Data sources: prefer local files; fallback to geoBoundaries ADM1 remote
  const DATASETS = {
    ph: {
      label: 'Philippines',
      local: '/data/geo/ph_adm1.geojson', // optional local override (official)
      // Prefer lightweight bundled file first to guarantee availability
      localCandidates: [
        '/data/geo/ph_regions.min.geojson',
        '/data/geo/ph_adm1.geojson',
      ],
  // NOTE: abstract demo shapes removed; use official ADM1 only
      localLegacy: null,
      remoteCandidates: [
  'https://rawcdn.githack.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson',
        'https://cdn.jsdelivr.net/gh/wmgeolab/geoBoundaries@main/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson',
  'https://media.githubusercontent.com/media/wmgeolab/geoBoundaries/main/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson?raw=1',
        'https://unpkg.com/@wmgeolab/geoBoundaries/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson',
        'https://raw.githubusercontent.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/PHL/ADM1/geoBoundaries-PHL-ADM1.geojson',
      ],
      view: { center: [12.8797, 121.7740], zoom: 5 },
    },
    id: {
      label: 'Indonesia',
      local: '/data/geo/id_adm1.geojson',
      localCandidates: [
        '/data/geo/id_adm1.geojson',
        '/data/geo/id_provinces.min.geojson',
      ],
  // NOTE: abstract demo shapes removed; use official ADM1 only
      localLegacy: null,
      remoteCandidates: [
  'https://rawcdn.githack.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson',
        'https://cdn.jsdelivr.net/gh/wmgeolab/geoBoundaries@main/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson',
  'https://media.githubusercontent.com/media/wmgeolab/geoBoundaries/main/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson?raw=1',
        'https://unpkg.com/@wmgeolab/geoBoundaries/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson',
        'https://raw.githubusercontent.com/wmgeolab/geoBoundaries/main/releaseData/gbOpen/IDN/ADM1/geoBoundaries-IDN-ADM1.geojson',
      ],
      view: { center: [-2.5489, 118.0149], zoom: 4 },
    },
  };

  async function tryFetch(url) {
    try {
      const res = await fetch(url, { cache: 'no-store' });
      if (!res.ok) return null;
      return await res.json();
    } catch { return null; }
  }

  async function loadGeo(code) {
    if (geoCache[code]) return geoCache[code];
    const cfg = DATASETS[code];
    // Try local candidates first (fast, offline)
    let json = null;
    const locals = cfg.localCandidates || (cfg.local ? [cfg.local] : []);
    for (const url of locals) {
      json = await tryFetch(url);
      if (json) {
        console.info('[map] Loaded local boundaries:', url);
        break;
      }
    }
  // Skip abstract legacy demo layers entirely
    if (!json) {
      // Prefer our backend proxy (same origin)
      const proxyUrl = `/api/geo/adm1?country=${code}`;
      console.info('[map] Fetching boundaries via proxy:', proxyUrl);
      json = await tryFetch(proxyUrl);
      if (!json) {
        // Try multiple remote CDNs for CORS-friendly access
        for (const url of (cfg.remoteCandidates || [])) {
          console.info('[map] Fetching boundaries:', url);
          json = await tryFetch(url);
          if (json) break;
        }
      }
      // As a last-resort, try local fallbacks again (covers cases where dev server path differs)
      if (!json && locals.length) {
        for (const url of locals) {
          json = await tryFetch(url);
          if (json) {
            console.info('[map] Loaded local boundaries (fallback):', url);
            break;
          }
        }
      }
    }
    geoCache[code] = json;
    return json;
  }

  async function loadCountryLayer(code) {
    const cfg = DATASETS[code];
    const geojson = await loadGeo(code);
    if (!geojson) {
      console.warn(`[map] Failed to load ${cfg.label} ADM1 boundaries`);
      updatePanel(cfg.label, '');
      return null;
    }
    const layer = buildGeoLayer(geojson, cfg.label);
    try {
      const count = Array.isArray(geojson.features) ? geojson.features.length : 0;
      console.info(`[map] Loaded ${cfg.label} ADM1 features:`, count);
    } catch {}
    if (code === 'ph') phGeoLayer = layer; else idGeoLayer = layer;
    // Group-level click fallback
    layer.on('click', (e) => {
      const f = e.layer?.feature || e.propagatedFrom?.feature;
      const props = f?.properties || {};
      const name = props.name || props.shapeName || props.NAME_1 || props.NAME || props.region || props.Province || props.province;
      if (name) onSelectRegion(cfg.label, name, e.layer);
    });
    return layer;
  }

  async function showCountry(code) {
    currentCountry = code;
    // Clear existing
    [phGeoLayer, idGeoLayer].forEach(g => { if (g && map.hasLayer(g)) map.removeLayer(g); });
    activeLayer = null;
    lastSelectedRegion = null;
  if (code === 'id' && typeof idGroupLayers !== 'undefined') idGroupLayers.clear();

    const layer = await loadCountryLayer(code);
    if (layer) {
      layer.addTo(map);
      try { map.fitBounds(layer.getBounds(), { padding: [20, 20] }); }
      catch { const v = DATASETS[code].view; map.setView(v.center, v.zoom); }
    } else {
      const v = DATASETS[code].view; map.setView(v.center, v.zoom);
    }
    updatePanel(DATASETS[code].label, '');
  }

  // No modal; we only update the left panel on click now.

  // --- Indonesia province -> 7 main geographical regions grouping ---
  // Ensure this is defined before first use in showCountry()
  let idGroupLayers = new Map();
  const normalize = (s='') => s.toString().trim().toLowerCase()
    .replaceAll('daerah istimewa yogyakarta','yogyakarta')
    .replaceAll('special region of yogyakarta','yogyakarta')
    .replaceAll('daerah khusus ibu kota jakarta','jakarta')
    .replaceAll('dki jakarta','jakarta')
    .replaceAll('kepulauan riau','riau islands')
    .replaceAll('kepulauan bangka belitung','bangka belitung islands')
    .replaceAll('papua barat daya','southwest papua')
    .replaceAll('papua pegunungan','highland papua')
    .replaceAll('papua tengah','central papua')
    .replaceAll('papua selatan','south papua')
    .replaceAll('papua barat','west papua')
    .replaceAll('nusa tenggara barat','west nusa tenggara')
    .replaceAll('nusa tenggara timur','east nusa tenggara');

  const ID_GROUP_MAP = {
    // Sumatra
    'aceh': 'Sumatra',
    'north sumatra': 'Sumatra',
    'west sumatra': 'Sumatra',
    'riau': 'Sumatra',
    'riau islands': 'Sumatra',
    'jambi': 'Sumatra',
    'bengkulu': 'Sumatra',
    'south sumatra': 'Sumatra',
    'bangka belitung islands': 'Sumatra',
    'lampung': 'Sumatra',
    // Java
    'banten': 'Java',
    'jakarta': 'Java',
    'west java': 'Java',
    'central java': 'Java',
    'yogyakarta': 'Java',
    'east java': 'Java',
    // Kalimantan (Borneo)
    'west kalimantan': 'Kalimantan',
    'central kalimantan': 'Kalimantan',
    'south kalimantan': 'Kalimantan',
    'east kalimantan': 'Kalimantan',
    'north kalimantan': 'Kalimantan',
    // Sulawesi (Celebes)
    'north sulawesi': 'Sulawesi',
    'gorontalo': 'Sulawesi',
    'central sulawesi': 'Sulawesi',
    'west sulawesi': 'Sulawesi',
    'south sulawesi': 'Sulawesi',
    'southeast sulawesi': 'Sulawesi',
    // Lesser Sunda (Bali & Nusa Tenggara)
    'bali': 'Bali & Nusa Tenggara',
    'west nusa tenggara': 'Bali & Nusa Tenggara',
    'east nusa tenggara': 'Bali & Nusa Tenggara',
    // Maluku
    'maluku': 'Maluku',
    'north maluku': 'Maluku',
    // Papua (incl. new provinces)
    'papua': 'Papua',
    'west papua': 'Papua',
    'southwest papua': 'Papua',
    'south papua': 'Papua',
    'central papua': 'Papua',
    'highland papua': 'Papua',
  };

  // For Indonesia highlighting, keep a map from group => array of layers
  // (initialized above)

  function registerIndonesiaLayer(feature, layer) {
    const props = feature.properties || {};
    const rawName = props.name || props.shapeName || props.NAME_1 || props.Province || props.province || props.NAME || '';
    const group = ID_GROUP_MAP[normalize(rawName)];
    if (!group) return; // skip if unknown
    if (!idGroupLayers.has(group)) idGroupLayers.set(group, []);
    idGroupLayers.get(group).push(layer);
    wireLayer(layer, 'Indonesia', group);
  }

  // Override buildGeoLayer to register Indonesia grouping
  function buildGeoLayer(geojson, countryLabel) {
    const getFeatureName = (props) => {
      return (
        props?.name ||
        props?.shapeName ||
        props?.NAME_1 ||
        props?.NAME ||
        props?.region ||
        props?.Province ||
        props?.province ||
        'Unknown'
      );
    };
    const layer = L.geoJSON(geojson, {
      style: () => baseStyle,
      pane: 'regions-pane',
      onEachFeature: (feature, lyr) => {
        if (countryLabel === 'Indonesia') {
          registerIndonesiaLayer(feature, lyr);
        } else {
          const name = getFeatureName(feature.properties || {});
          lyr.options.interactive = true;
          wireLayer(lyr, countryLabel, name);
        }
      },
    });
    return layer;
  }

  // When a region is selected, if Indonesia show modal with group; also highlight all layers in the group
  async function onSelectRegion(country, regionName, layer) {
    // styling reset
    if (currentCountry === 'id') {
      // reset all province styles
      idGroupLayers.forEach(arr => arr.forEach(l => l.setStyle(baseStyle)));
      const groupLayers = idGroupLayers.get(regionName) || [];
      groupLayers.forEach(l => l.setStyle(activeStyle));
      activeLayer = null; // handled per group
    } else {
      if (activeLayer) activeLayer.setStyle(baseStyle);
      activeLayer = layer;
      layer?.setStyle(activeStyle);
    }

    lastSelectedRegion = regionName;
    currentCountry = country.toLowerCase().startsWith('ph') || country === 'Philippines' ? 'ph' : 'id';
    // Persist and deep link
    saveSelection(currentCountry, regionName);
    setQuery(currentCountry, regionName);

    // Update left panel as before
    updatePanel(country, regionName);

  // No modal popup.
  }

  // Initialize state from query or storage
  (async () => {
    const params = new URLSearchParams(window.location.search);
    const qCountry = (params.get('country') || '').toLowerCase();
    const qRegion = params.get('region') || '';
    const qLevel = (params.get('level') || '').toLowerCase();
    const persisted = readSelection();
    const startCountry = ['ph','id'].includes(qCountry) ? qCountry : (persisted?.country || 'ph');
    currentLevel = ['adm1','adm2'].includes(qLevel) ? qLevel : (persisted?.level || 'adm1');
    await showCountry(startCountry);

    // Reflect switch UI
    const switchEl = document.getElementById('country-switch');
    if (switchEl) {
      switchEl.querySelectorAll('.pill-option').forEach(b => b.classList.remove('active'));
      const btn = switchEl.querySelector(`[data-country="${startCountry}"]`);
      btn?.classList.add('active');
    }

    // reflect level switch UI
    const levelSwitchEl = document.getElementById('level-switch');
    if (levelSwitchEl) {
      levelSwitchEl.querySelectorAll('.pill-option').forEach(b => b.classList.remove('active'));
      const btn = levelSwitchEl.querySelector(`[data-level="${currentLevel}"]`);
      btn?.classList.add('active');
    }

    // If region provided, try to find and select it
    const regionToSelect = qRegion || persisted?.region || '';
    if (regionToSelect) {
      if (startCountry === 'ph') {
        const layer = phGeoLayer;
        layer?.eachLayer(lyr => {
          const props = lyr.feature?.properties || {};
          const name = props.name || props.shapeName || props.NAME_1 || props.NAME || props.region || props.Province || props.province;
          if (name && name.toLowerCase() === regionToSelect.toLowerCase()) {
            onSelectRegion('Philippines', name, lyr);
          }
        });
      } else {
        // Indonesia: regionToSelect is a group name
        const groupLayers = idGroupLayers.get(regionToSelect) || idGroupLayers.get(regionToSelect[0]?.toUpperCase()+regionToSelect.slice(1)) || [];
        if (groupLayers.length) onSelectRegion('Indonesia', regionToSelect, groupLayers[0]);
      }
    }
  })();

  // Switch handling
  const switchEl = document.getElementById('country-switch');
  if (switchEl) {
    switchEl.addEventListener('click', (e) => {
      const btn = e.target.closest('.pill-option');
      if (!btn) return;
      const code = btn.getAttribute('data-country');

      switchEl.querySelectorAll('.pill-option').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      showCountry(code);
    });
  }

  // ADM level switch (ADM1 active; ADM2 placeholder)
  const levelSwitch = document.getElementById('level-switch');
  if (levelSwitch) {
    levelSwitch.addEventListener('click', (e) => {
      const btn = e.target.closest('.pill-option');
      if (!btn) return;
      const level = btn.getAttribute('data-level');
      if (level === 'adm2') return; // not implemented yet
      levelSwitch.querySelectorAll('.pill-option').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentLevel = level;
      // persist and reflect URL
      saveSelection(currentCountry, lastSelectedRegion || '');
      setQuery(currentCountry, lastSelectedRegion || '');
    });
  }
});
