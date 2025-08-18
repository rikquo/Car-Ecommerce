const gsap = window.gsap;
const ScrollTrigger = window.ScrollTrigger;

gsap.registerPlugin(ScrollTrigger);

// Initial animations
gsap.from("nav", { opacity: 0, y: -50, duration: 1 });

gsap.from(".models-title", {
  opacity: 0,
  y: 60,
  duration: 1.2,
  delay: 0.4,
});

gsap.from(".models-subtitle", {
  opacity: 0,
  y: 40,
  duration: 1.2,
  delay: 0.6,
});

gsap.from(".breadcrumb", {
  opacity: 0,
  y: 20,
  duration: 1,
  delay: 0.8,
});

// Navbar hide/show on scroll
let lastScroll = 0;
const navbar = document.querySelector(".navbar");

window.addEventListener("scroll", () => {
  const currentScroll = window.pageYOffset;

  if (currentScroll > lastScroll && currentScroll > 100) {
    gsap.to(navbar, { y: -100, opacity: 0, duration: 0.3, ease: "power2.out" });
  } else {
    gsap.to(navbar, { y: 0, opacity: 1, duration: 0.3, ease: "power2.out" });
  }

  lastScroll = currentScroll;
});

// Series title animations
gsap.from(".series-title", {
  opacity: 0,
  y: 50,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".series-title",
    start: "top 80%",
    toggleActions: "play none none reverse",
  },
  stagger: 0.3,
});

gsap.from(".series-description", {
  opacity: 0,
  y: 30,
  duration: 1,
  scrollTrigger: {
    trigger: ".series-description",
    start: "top 80%",
    toggleActions: "play none none reverse",
  },
  stagger: 0.3,
});

// Replace the car cards animation with this optimized version
gsap.utils.toArray(".car-card").forEach((card, i) => {
  gsap.from(card, {
    opacity: 0,
    y: 60,
    duration: 0.8,
    scrollTrigger: {
      trigger: card,
      start: "top 85%",
      toggleActions: "play none none none",
    },
    delay: i * 0.1, // Stagger delay
  });
});

// Individual car card hover effects
document.querySelectorAll(".car-card").forEach((card) => {
  card.addEventListener("mouseenter", () => {
    gsap.to(card.querySelector(".car-image img"), {
      scale: 1.1,
      duration: 0.4,
      ease: "power2.out",
    });
  });

  card.addEventListener("mouseleave", () => {
    gsap.to(card.querySelector(".car-image img"), {
      scale: 1,
      duration: 0.4,
      ease: "power2.out",
    });
  });
});

// Details button hover effects
document.querySelectorAll(".details-btn").forEach((btn) => {
  btn.addEventListener("mouseenter", () => {
    gsap.to(btn, {
      scale: 1.05,
      duration: 0.3,
      ease: "power2.out",
    });
  });

  btn.addEventListener("mouseleave", () => {
    gsap.to(btn, {
      scale: 1,
      duration: 0.3,
      ease: "power2.out",
    });
  });
});

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));

    if (target) {
      const offsetTop = target.offsetTop - 120; // Account for fixed navbar

      window.scrollTo({
        top: offsetTop,
        behavior: "smooth",
      });
    }
  });
});

// Footer animations
gsap.from(".footer", {
  opacity: 0,
  y: 100,
  duration: 1.5,
  ease: "power2.out",
  scrollTrigger: {
    trigger: ".footer",
    start: "top 90%",
    end: "top 60%",
    toggleActions: "play none none reverse",
  },
});

gsap.from(".footer-tagline", {
  opacity: 0,
  y: 50,
  duration: 1.2,
  delay: 0.3,
  ease: "power2.out",
  scrollTrigger: {
    trigger: ".footer",
    start: "top 80%",
    toggleActions: "play none none reverse",
  },
});

gsap.from(".footer-btn", {
  opacity: 0,
  scale: 0.8,
  duration: 1,
  delay: 0.5,
  ease: "back.out(1.7)",
  scrollTrigger: {
    trigger: ".footer",
    start: "top 80%",
    toggleActions: "play none none reverse",
  },
});

gsap.from(".footer-nav a", {
  opacity: 0,
  x: 50,
  duration: 0.8,
  stagger: 0.1,
  delay: 0.4,
  ease: "power2.out",
  scrollTrigger: {
    trigger: ".footer-nav",
    start: "top 85%",
    toggleActions: "play none none reverse",
  },
});

gsap.from(".footer-social a", {
  opacity: 0,
  x: 30,
  duration: 0.8,
  stagger: 0.15,
  delay: 0.6,
  ease: "power2.out",
  scrollTrigger: {
    trigger: ".footer-social",
    start: "top 85%",
    toggleActions: "play none none reverse",
  },
});

gsap.from(".footer-location p", {
  opacity: 0,
  y: 20,
  duration: 0.8,
  stagger: 0.1,
  delay: 0.7,
  ease: "power2.out",
  scrollTrigger: {
    trigger: ".footer-location",
    start: "top 90%",
    toggleActions: "play none none reverse",
  },
});

gsap.from(".footer-contact p", {
  opacity: 0,
  y: 20,
  duration: 0.8,
  stagger: 0.1,
  delay: 0.8,
  ease: "power2.out",
  scrollTrigger: {
    trigger: ".footer-contact",
    start: "top 90%",
    toggleActions: "play none none reverse",
  },
});

// Footer hover effects
document.querySelectorAll(".footer-nav a").forEach((link) => {
  link.addEventListener("mouseenter", () => {
    gsap.to(link, { scale: 1.05, duration: 0.3, ease: "power2.out" });
  });

  link.addEventListener("mouseleave", () => {
    gsap.to(link, { scale: 1, duration: 0.3, ease: "power2.out" });
  });
});

document.querySelectorAll(".footer-social a").forEach((link) => {
  link.addEventListener("mouseenter", () => {
    gsap.to(link, { x: 5, duration: 0.3, ease: "power2.out" });
  });

  link.addEventListener("mouseleave", () => {
    gsap.to(link, { x: 0, duration: 0.3, ease: "power2.out" });
  });
});

const footerBtn = document.querySelector(".footer-btn");
if (footerBtn) {
  footerBtn.addEventListener("mouseenter", () => {
    gsap.to(footerBtn, {
      scale: 1.1,
      duration: 0.3,
      ease: "power2.out",
      boxShadow: "0 10px 30px rgba(255, 255, 255, 0.2)",
    });
  });

  footerBtn.addEventListener("mouseleave", () => {
    gsap.to(footerBtn, {
      scale: 1,
      duration: 0.3,
      ease: "power2.out",
      boxShadow: "none",
    });
  });
}

// Parallax effect for series sections
gsap.to(".series-section:nth-child(even)", {
  y: -50,
  scrollTrigger: {
    trigger: ".series-section:nth-child(even)",
    start: "top bottom",
    end: "bottom top",
    scrub: 1,
  },
});

// Stagger animation for car specs
gsap.from(".spec-item", {
  opacity: 0,
  x: -20,
  duration: 0.6,
  stagger: 0.1,
  scrollTrigger: {
    trigger: ".spec-item",
    start: "top 90%",
    toggleActions: "play none none reverse",
  },
});

// Pop-up modal functionality
document.addEventListener("DOMContentLoaded", () => {
  const moreDetailsBtns = document.querySelectorAll(".details-btn");

  moreDetailsBtns.forEach((button) => {
    button.addEventListener("click", (event) => {
      event.preventDefault();
      const targetId = event.target.getAttribute("data-target");
      const modal = document.querySelector(targetId);
      if (modal) {
        modal.style.display = "flex";
      }
    });
  });

  // Close the modal when the close button is clicked
  document.querySelectorAll(".close-btn").forEach((closeBtn) => {
    closeBtn.addEventListener("click", () => {
      const modal = closeBtn.closest(".modal");
      modal.style.display = "none";
    });
  });

  // Close the modal if the user clicks outside of the content
  window.addEventListener("click", (event) => {
    if (event.target.classList.contains("modal")) {
      event.target.style.display = "none";
    }
  });

  document
    .querySelectorAll(".modal-buttons .add-to-garage-btn")
    .forEach((btn) => {
      btn.addEventListener("click", (e) => {
        // Get the car ID from the modal
        const modal = btn.closest(".modal");
        const modalId = modal.id;
        const carId = modalId.replace("car-details-modal-", "");

        // Create and submit form
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "/pages/add_to_garage.php";

        const carIdInput = document.createElement("input");
        carIdInput.type = "hidden";
        carIdInput.name = "car_id";
        carIdInput.value = carId;

        form.appendChild(carIdInput);
        document.body.appendChild(form);
        form.submit();
      });
    });

  // Wishlist functionality remains the same
  document.querySelectorAll(".modal-buttons .wishlist-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      // This is handled by the existing PHP form submission
      // No changes needed here
    });
  });

  // Color selector functionality
  const colorOptions = document.querySelectorAll(".color-option");

  colorOptions.forEach((option) => {
    option.addEventListener("click", function () {
      const carId = this.getAttribute("data-car-id");
      const selectedColor = this.getAttribute("data-color");

      // Remove active class from all color options for this car
      const carColorOptions = document.querySelectorAll(
        `[data-car-id="${carId}"].color-option`
      );
      carColorOptions.forEach((opt) => opt.classList.remove("active"));

      // Add active class to clicked option
      this.classList.add("active");

      // Apply color filter to car image
      const carImage = document.querySelector(`img[data-car-id="${carId}"]`);
      if (carImage) {
        // Remove all existing color filter classes
        carImage.classList.remove(
          "color-filter-white",
          "color-filter-black",
          "color-filter-red",
          "color-filter-blue",
          "color-filter-silver",
          "color-filter-yellow",
          "color-filter-green",
          "color-filter-orange"
        );

        // Add new color filter class
        if (selectedColor !== "white") {
          // White is default, no filter needed
          carImage.classList.add(`color-filter-${selectedColor}`);
        }

        // Add smooth transition effect
        gsap.to(carImage, {
          scale: 1.05,
          duration: 0.2,
          ease: "power2.out",
          onComplete: () => {
            gsap.to(carImage, {
              scale: 1,
              duration: 0.2,
              ease: "power2.out",
            });
          },
        });
      }
    });

    // Add hover effects to color options
    option.addEventListener("mouseenter", function () {
      if (!this.classList.contains("active")) {
        gsap.to(this, {
          scale: 1.1,
          duration: 0.2,
          ease: "power2.out",
        });
      }
    });

    option.addEventListener("mouseleave", function () {
      if (!this.classList.contains("active")) {
        gsap.to(this, {
          scale: 1,
          duration: 0.2,
          ease: "power2.out",
        });
      }
    });
  });

  const searchInput = document.querySelector(".search-input");
  const searchForm = document.querySelector(".search-form");

  if (searchInput && searchForm) {
    // Add search on Enter key
    searchInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        searchForm.submit();
      }
    });

    // Add visual feedback for search input
    searchInput.addEventListener("focus", function () {
      gsap.to(this.parentElement, {
        scale: 1.02,
        duration: 0.3,
        ease: "power2.out",
      });
    });

    searchInput.addEventListener("blur", function () {
      gsap.to(this.parentElement, {
        scale: 1,
        duration: 0.3,
        ease: "power2.out",
      });
    });
  }

  // Filter button animations
  document.querySelectorAll(".filter-btn").forEach((btn) => {
    btn.addEventListener("mouseenter", () => {
      if (!btn.classList.contains("active")) {
        gsap.to(btn, {
          scale: 1.05,
          duration: 0.3,
          ease: "power2.out",
        });
      }
    });

    btn.addEventListener("mouseleave", () => {
      if (!btn.classList.contains("active")) {
        gsap.to(btn, {
          scale: 1,
          duration: 0.3,
          ease: "power2.out",
        });
      }
    });
  });

  // Animate results summary
  const resultsSummary = document.querySelector(".results-summary");
  if (resultsSummary) {
    gsap.from(resultsSummary, {
      opacity: 0,
      y: 20,
      duration: 0.8,
      delay: 0.3,
      ease: "power2.out",
    });
  }

  // Animate no results section
  const noResultsSection = document.querySelector(".no-results-section");
  if (noResultsSection) {
    gsap.from(".no-results-content", {
      opacity: 0,
      y: 50,
      duration: 1,
      ease: "power2.out",
    });
  }
});
