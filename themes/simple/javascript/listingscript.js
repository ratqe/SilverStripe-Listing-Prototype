
document.addEventListener("DOMContentLoaded", function () {
  const overlayCarouselEl = document.querySelector("#listingCarouselOverlay");
  const inlineCarouselEl = document.querySelector("#listingCarouselInline");

  if (!overlayCarouselEl || !inlineCarouselEl) return;

  // Initialize both carousels without auto-slide
  const overlayCarousel = new bootstrap.Carousel(overlayCarouselEl, { interval: false, ride: false });
  const inlineCarousel = new bootstrap.Carousel(inlineCarouselEl, { interval: false, ride: false });

  // Sync overlay carousel to inline carousel, but not vice versa
  inlineCarouselEl.addEventListener("slid.bs.carousel", function (e) {
    overlayCarousel.to(e.to);
  });

  // Function to open overlay and start on the same slide
  window.imagesOverlayOn = function () {
    const overlay = document.getElementById("images-gallery-overlay");
    overlay.style.display = "block";

    // Get current inline active slide
    const activeIndex = Array.from(inlineCarouselEl.querySelectorAll(".carousel-item")).findIndex(
      item => item.classList.contains("active")
    );

    // Show same slide in overlay
    overlayCarousel.to(activeIndex);
  };

  // Close overlay
  window.overlayOff = function () {
    document.getElementById("images-gallery-overlay").style.display = "none";
  };

  window.floorPlanOverlayOn = function () {
    const overlay = document.getElementById("floorplan-gallery-overlay");
    overlay.style.display = "block";
  };

  window.floorPlanOverlayOff = function () {
    document.getElementById("floorplan-gallery-overlay").style.display = "none";
  };
});
