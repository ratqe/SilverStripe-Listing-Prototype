<!-- Load Leaflet + minimal CSS/JS *inline* so we don't depend on $Requirements -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
  /* keep the map visible even if your theme CSS isn't loaded */
  .property-map__canvas { width:100%; height:380px; border-radius:12px; overflow:hidden; }
  .property-map__address { margin-bottom:.5rem; font-weight:600; }
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('map');
    if (!el || typeof L === 'undefined') return;

    var lat = parseFloat(el.getAttribute('data-lat'));
    var lng = parseFloat(el.getAttribute('data-lng'));
    if (isNaN(lat) || isNaN(lng)) return;

    var map = L.map(el).setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 20,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.marker([lat, lng]).addTo(map);
  });
</script>


<article class="content typography">
<p style="background:#ff0;padding:.5rem">PROPERTY PAGE LAYOUT LOADED</p>
  $Content

  <h2>Location</h2>
  <% include PropertyMap %>
</article>


