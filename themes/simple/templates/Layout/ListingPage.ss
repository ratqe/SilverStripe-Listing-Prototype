<% require css('themes/simple/css/listing.css') %>
<% require javascript('themes/simple/javascript/listingscript.js', 'defer') %>


         <!-- image gallery overlay -->
    <!-- maybe remove the thumbnail and have the carousel, but also include a gallery to see all images -->

    <!-- open button (temp) -->
<article>
<div id="images-gallery-overlay">

    <div class="images-gallery-container">
        <% if $ListingImageObjects.exists %>
        <!-- BIG IMAGE GALLERY -->
        <div id="listingCarouselOverlay" class="carousel mb-5">

            <!-- Slides -->
            <div class="carousel-inner">
                <% loop $ListingImageObjects %>
                    <% if $ImageFile %>
                        <div class="carousel-item<% if $Pos(0) = 0 %> active<% end_if %>">
                            <img src="$ImageFile.URL"
                                alt="<% if $Caption %>$Caption<% else %>Slide $Pos(1)<% end_if %>"
                                class="d-block w-100" />
                        </div>
                    <% end_if %>
                <% end_loop %>
            </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#listingCarouselOverlay" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#listingCarouselOverlay" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>

            <!-- Thumbnails / Indicators -->
            <div class="carousel-indicators thumbnails">
                <% loop $ListingImageObjects %>
                    <% if $ImageFile %>
                        <button type="button"
                                data-bs-target="#listingCarouselOverlay"
                                data-bs-slide-to="$Pos(0)"
                                class="<% if $Pos(0) = 0 %>active<% end_if %>"
                                <% if $Pos(0) = 0 %>aria-current="true"<% end_if %>
                                aria-label="Slide $Pos(1)">
                            <img class="d-block w-100"
                                src="$ImageFile.URL"
                                alt="<% if $Caption %>$Caption<% else %>Slide $Pos(1)<% end_if %>" />
                        </button>
                    <% end_if %>
                <% end_loop %>
            </div>

        </div>
        <% end_if %>
    </div>

    <!-- Exit Button -->
    <div onclick="overlayOff()" id="exit-button">
        <i class="fa-solid fa-xmark fa-2xl" style="color: #ffffff;"></i>
    </div>

</div>

    
    <!-- BIG IMAGE GALLERY -->
    <% if $ListingImageObjects.exists %>
    <div class="gallery-container">
        <div id="listingCarouselInline" class="carousel slide mb-5">

            <!-- Slides -->
            <div class="carousel-inner">
                <% loop $ListingImageObjects %>
                    <% if $ImageFile %>
                        <div class="carousel-item<% if $Pos(0) = 0 %> active<% end_if %>">
                            <img src="$ImageFile.URL"
                                alt="<% if $Caption %>$Caption<% else %>Slide $Pos(1)<% end_if %>"
                                class="d-block w-100" />
                        </div>
                    <% end_if %>
                <% end_loop %>
            </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#listingCarouselInline" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#listingCarouselInline" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <div class="gallery-right">
            <button onclick="overlayOn()" class="btn btn-primary">Image gallery</button>
            <button class="btn btn-primary">Floor Plan</button>
        </div>
        </div>
    <% end_if %>

    <!-- ADDRESS TITLE -->

    <div class="address-container">
    <h1>$Address</h1>
    </div>


    <!-- feature section -->
    <div class="feature-section d-inline-flex">
        <div class="feature-box">         
        <% if $HouseTypeHouse %>
            House
        <% end_if %>
        <% if $HouseTypeTownhouse %>
            Townhouse
        <% end_if %> </div>
        <% if $Bedrooms %>
        <div class="feature-box"><i class="fa-solid fa-bed"></i> $Bedrooms </div>
        <% end_if %>
        <% if $Bathrooms %>
        <div class="feature-box"><i class="fa-solid fa-bath"></i></i> $Bathrooms </div>
        <% end_if %>
        <div class="feature-box">another field</div>
        <div class="feature-box">another field</div>
    </div>
    <!-- feature section -->

    <!-- description box -->
    <div class="listing-description-container">
        <p>$Content</p>
    </div>

        <div class="listing-quality-container">
        <h2>Quality Features</h2>
        <div class="d-flex align-content-start flex-wrap">
            <% if $QualityAppliances %>
                <div class="listing-quality-box">Quality Appliances</div>
            <% end_if %>
            <% if $HasAC %>
                <div class="listing-quality-box">Air Conditioning</div>
            <% end_if %>
            <% if $IsFurnished %>
                <div class="listing-quality-box">Fully Furnished</div>
            <% end_if %>
        </div>
    </div>

    <div class="listing-feature-container">
        <h2>Additional Information</h2>
        <div class="d-flex align-content-start flex-wrap">
            <% if $FloorSpace %>
                <div class="listing-feature-box">$FloorSpace m² Floor Space</div>
            <% end_if %>

            <% if $LandArea %>
                <div class="listing-feature-box">$LandArea m² Land Area</div>
            <% end_if %>

            <% if $IsFenced %>
                <div class="listing-feature-box">Fenced</div>
            <% end_if %>

            <div class="listing-feature-box"> Built in $YearMade </div>
        </div>
    </div>

    <div>
        <h2>Contact an Agent</h2>
        <% loop $Contacts %>
        <div>
            <p>$ContactName</p>
            <p>$ContactPhone</p>
            <p>$ContactEmail</p>
        </div>
        <% end_loop %>
    </div>

</article>