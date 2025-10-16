(function(){
  // debug
  console.log("map code functioning");

  // Helpers
  function fmtDist(m){ return m < 1000 ? Math.round(m)+' m' : (m/1000).toFixed(2).replace(/\.00$/,'')+' km'; }
  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  // Elements
  var mapEl = document.getElementById('map');
  if (!mapEl || typeof L === 'undefined') return;

  // Property coords from data-*
  var homeLat = parseFloat(mapEl.getAttribute('data-lat'));
  var homeLng = parseFloat(mapEl.getAttribute('data-lng'));
  if (isNaN(homeLat) || isNaN(homeLng) || (homeLat===0 && homeLng===0)) {
    console.warn('No valid property coordinates yet.');
    return;
  }

  // Radii
  var RADIUS_POI = 1500;      // 1.5 km for quick filter buttons (Overpass)
  var RADIUS_SEARCH = 100000; // 100 km for free-text search (Nominatim)

  // Map
  var map = L.map(mapEl).setView([homeLat, homeLng], 16);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    minZoom: 0,
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);
  var homeMarker = L
    .marker([homeLat, homeLng])
    .addTo(map)
    .bindPopup('Property')
    .openPopup();

  // POI layer + icon
  var poiLayer = L.layerGroup().addTo(map);
  var poiIcon = L.icon({
    iconUrl:'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    iconSize:[25,41], iconAnchor:[12,41], popupAnchor:[1,-34]
  });

  // UI
  var qInput = document.getElementById('poiQuery');
  var qBtn   = document.getElementById('poiGo');
  var resultsEl = document.getElementById('results');

  // -------- Overpass (1.5 km) --------
  async function overpassSearch(overpassBody){
    var q = `[out:json][timeout:25];
(
  ${overpassBody}(around:${RADIUS_POI},${homeLat},${homeLng});
  way${overpassBody.replace(/node/g,'')}(around:${RADIUS_POI},${homeLat},${homeLng});
  relation${overpassBody.replace(/node/g,'')}(around:${RADIUS_POI},${homeLat},${homeLng});
);
out center;`;
    var url = 'https://overpass-api.de/api/interpreter?data=' + encodeURIComponent(q);
    const res = await fetch(url);
    if (!res.ok) throw new Error('Overpass error: ' + res.status);
    const data = await res.json();
    return (data.elements||[]).map(e => {
      var lat = e.lat || (e.center && e.center.lat);
      var lng = e.lon || (e.center && e.center.lon);
      if (!lat || !lng) return null;
      var tags = e.tags || {};
      var name = tags.name || tags.ref || '(Unnamed)';
      var cat  = tags.amenity || tags.shop || tags.highway || tags.public_transport || '';
      return {lat, lng, name, cat};
    }).filter(Boolean);
  }

  // -------- Nominatim (100 km) --------
  async function nominatimSearch(query){
    // Convert 100 km to degrees at this latitude
    var latRad = homeLat * Math.PI / 180;
    var dLat = RADIUS_SEARCH / 111320;                           // ~111.32 km per 1° lat
    var dLng = RADIUS_SEARCH / (111320 * Math.cos(latRad) || 1); // handle high latitudes

    var viewbox = [homeLng - dLng, homeLat - dLat, homeLng + dLng, homeLat + dLat].join(',');
    var url = 'https://nominatim.openstreetmap.org/search?format=jsonv2'
            + '&q=' + encodeURIComponent(query)
            + '&limit=50&addressdetails=0'
            + '&viewbox=' + encodeURIComponent(viewbox)
            + '&bounded=1';

    const res = await fetch(url, { headers: {'Accept-Language':'en'}, referrerPolicy:'no-referrer' });
    if (!res.ok) throw new Error('Nominatim error: ' + res.status);
    const data = await res.json();

    const items = data.map(r => ({
      lat: parseFloat(r.lat),
      lng: parseFloat(r.lon),
      name: (r.display_name||'').split(',')[0] || r.type,
      cat: r.category || r.type
    }));

    // Keep only within 100 km great-circle distance
    return items.filter(i => map.distance([homeLat,homeLng],[i.lat,i.lng]) <= RADIUS_SEARCH);
  }

  // Render POIs on map + list
  function renderPOIs(items, radiusMeters){
    poiLayer.clearLayers();
    resultsEl.innerHTML = '';

    if (!items.length){
      resultsEl.innerHTML = '<div class="muted" style="color:#666">No results within ' + (radiusMeters/1000) + ' km.</div>';
      return;
    }

    // add distance + sort
    items.forEach(i => i.dist = map.distance([homeLat,homeLng],[i.lat,i.lng]));
    items.sort((a,b) => a.dist - b.dist);

    var bounds = L.latLngBounds([[homeLat,homeLng]]);
    items.forEach(i => {
      var m = L.marker([i.lat,i.lng], {icon: poiIcon})
               .bindPopup('<strong>'+escapeHtml(i.name)+'</strong><br>'+escapeHtml(i.cat||'')+'<br>'+fmtDist(i.dist)+' from property');
      poiLayer.addLayer(m);
      bounds.extend([i.lat,i.lng]);

      var row = document.createElement('div');
      row.innerHTML = '<div><strong>'+escapeHtml(i.name)+'</strong></div>'
                    + '<div class="muted" style="color:#666">'+escapeHtml(i.cat||'')+' • '+fmtDist(i.dist)+'</div>';
      row.addEventListener('click', function(){ map.flyTo([i.lat,i.lng], 17, {duration:.6}); m.openPopup(); });
      resultsEl.appendChild(row);
    });

    if (items.length) map.fitBounds(bounds, {padding:[20,20]});
  }

  // Wire quick filter buttons (1.5 km)
  document.querySelectorAll('.qbtn').forEach(btn => {
    btn.style.padding = '.35rem .6rem';
    btn.style.border = '1px solid #ddd';
    btn.style.borderRadius = '.6rem';
    btn.style.background = '#fafafa';
    btn.addEventListener('click', async function(){
      try {
        resultsEl.textContent = 'Searching…';
        const overBody = this.getAttribute('data-overpass');
        const items = await overpassSearch(overBody);
        renderPOIs(items, RADIUS_POI);
      } catch(e){
        console.error(e);
        resultsEl.innerHTML = '<div style="color:#b00">Search failed. Try again.</div>';
      }
    });
  });

  // Wire free-text search (100 km)
  if (qBtn && qInput){
    qBtn.addEventListener('click', async function(){
      const q = (qInput.value||'').trim();
      if (!q) return;
      try {
        resultsEl.textContent = 'Searching…';
        const items = await nominatimSearch(q);
        renderPOIs(items, RADIUS_SEARCH);
      } catch(e){
        console.error(e);
        resultsEl.innerHTML = '<div style="color:#b00">Search failed. Try again.</div>';
      }
    });
  }
})();