<h1>$Title</h1>

<div class="filters" style="margin-bottom: 1rem;">
    $FilterForm
    <p style="margin-top:.5rem;">
        <a href="$Link" class="btn btn-sm">Reset filters</a>
    </p>
</div>

<% if $Listings.Exists %>
    <p>Showing $Listings.TotalItems results</p>

    <ul class="listing-grid" style="list-style:none; padding:0; margin:1rem 0; display:grid; grid-template-columns: repeat(auto-fill, minmax(260px,1fr)); gap:16px;">
        <% loop $Listings %>
            <li class="card" style="border:1px solid #eee; border-radius:10px; padding:14px; background:#fff;">
                <h3 style="margin:0 0 .25rem 0;"><a href="$Link">$Title</a></h3>

                <% if $Address %>
                    <p style="margin:.25rem 0;">$Address</p>
                <% end_if %>

                <% if $Cost %>
                    <p style="margin:.25rem 0;">Rent: $Cost.Nice per week</p>
                <% end_if %>

                <p style="margin:.25rem 0;">
                    <% if $Bedrooms %>Bedrooms: $Bedrooms<% end_if %>
                    <% if $Bathrooms %> | Bathrooms: $Bathrooms<% end_if %>
                    <% if $Carparks %> | Carparks: $Carparks<% end_if %>
                </p>

                <% if $FloorSpace || $LandArea %>
                    <p style="margin:.25rem 0; font-size:.95em; color:#555;">
                        <% if $FloorSpace %>$FloorSpace m² floor<% end_if %>
                        <% if $FloorSpace && $LandArea %> | <% end_if %>
                        <% if $LandArea %>$LandArea m² land<% end_if %>
                    </p>
                <% end_if %>

                <% if $Availability %>
                    <span class="badge" style="display:inline-block; background:#e6f7ec; color:#0a8a3c; border:1px solid #bfe7cb; border-radius:6px; padding:.15rem .45rem; font-size:.85em;">Available</span>
                <% end_if %>
            </li>
        <% end_loop %>
    </ul>

    <!-- Built-in pagination from PaginatedList -->
    <div class="pagination" style="margin-top:1rem;">
        $Listings.Pagination
    </div>

<% else %>
    <p>No listings found matching your criteria.</p>
<% end_if %>
