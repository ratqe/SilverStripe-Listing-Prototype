<% if $HasCoords %>
  <div class="property-map">
    <div id="map" class="property-map__canvas" data-lat="$Latitude" data-lng="$Longitude"></div>
    <noscript>
      <p>Map requires JavaScript. You can still view it on 
        <a href="$GoogleMapsLink" target="_blank" rel="noopener">Google Maps</a>.
      </p>
    </noscript>
  </div>
<% else %>
  <% if $GoogleMapsLink %>
    <p><a href="$GoogleMapsLink" target="_blank" rel="noopener">View on Google Maps</a></p>
  <% else %>
    <p>Location information is not available.</p>
  <% end_if %>
<% end_if %>
