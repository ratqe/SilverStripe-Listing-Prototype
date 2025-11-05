<% require css('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css') %>
<% require css('themes/simple/css/map.css') %>
<% require javascript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js') %>
<% require javascript('themes/simple/javascript/mapfilterscript.js', 'defer') %>

<div class="map-section-container d-flex">

    <!-- LEFT SIDE: Search + Amenities -->
    <div class="map-section-left">

        <!-- Address + Search -->
        <div class="map-search-container">
            <h3>$Address</h3>

            <!-- Search bar -->
            <div class="poi-controls" style="margin:.5rem 0 1rem; display:grid; gap:.5rem">
                <div style="display:flex; gap:.5rem; align-items:center;">
                    <input id="poiQuery" type="text" placeholder="Search nearby… e.g. bus stop, supermarket, Woolworths" style="flex:1; padding:.5rem; border:1px solid #ccc; border-radius:.5rem;">
                    <button id="poiGo" type="button" style="padding:.5rem .75rem; border:1px solid #ccc; border-radius:.5rem; background:#f7f7f7; cursor:pointer;">Search nearby</button>
                </div>

                <!-- Quick Filters -->
                <div class="quick-filters" style="display:flex; flex-wrap:wrap; gap:.5rem;">
                    <button class="qbtn" type="button" data-overpass='node["highway"="bus_stop"]' title="Bus stops">Bus stops</button>
                    <!--<button class="qbtn" type="button" data-overpass='node["amenity"="supermarket"];node["shop"="supermarket"]' title="Supermarkets">Supermarkets</button>-->
                    <button class="qbtn" type="button" data-overpass='node["shop"="supermarket"]["name"~"Woolworths|Countdown",i]' title="Woolworths">Woolworths</button>
                    <button class="qbtn" type="button" data-overpass='node["amenity"="school"]' title="Schools">Schools</button>
                    <button class="qbtn" type="button" data-overpass='node["amenity"="pharmacy"]' title="Pharmacies">Pharmacies</button>
                </div>

                <small class="muted">
                    Quick filters search within <strong>1.5 km</strong>. Free-text “Search nearby” looks within <strong>100 km</strong>.
                </small>
            </div>
        </div>

        <!-- Amenities Tabs -->
        <div class="amenities-container">

            <div id="results"></div>

        </div>
    </div>

    <!-- RIGHT SIDE: Map -->
    <div class="map-section-right">
        <% include PropertyMap %>
        
    </div>
</div>
