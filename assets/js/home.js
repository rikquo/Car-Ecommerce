// Import GSAP library (keep existing)
const gsap = window.gsap;

gsap.from("nav", { opacity: 0, y: -50, duration: 1 });
gsap.from(".rev-title", { opacity: 0, y: 60, duration: 1.2, delay: 0.4 });
gsap.from(".rev", { opacity: 0, y: 60, duration: 1.2, delay: 0.6 });
gsap.from(".image", { opacity: 0, y: 50, duration: 1.2, delay: 0.2 });

let lastScroll = 0;
const navbar = document.querySelector(".nav");

window.addEventListener("scroll", () => {
  const currentScroll = window.pageYOffset;

  if (currentScroll > lastScroll && currentScroll > 100) {
    gsap.to(navbar, { y: -100, opacity: 0, duration: 0.1, ease: "power2.out" });
  } else {
    gsap.to(navbar, { y: 0, opacity: 1, duration: 0.1, ease: "power2.out" });
  }

  lastScroll = currentScroll;
});

// ===== SMOOTH SCROLLING FOR NAVIGATION ===== (keep existing)
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));

    if (target) {
      const offsetTop = target.offsetTop - 100; // Account for fixed navbar

      window.scrollTo({
        top: offsetTop,
        behavior: "smooth",
      });
    }
  });
});

// Hero section zoom effect (keep existing)
gsap.to(".hero-wrapper", {
  scale: 1.5,
  scrollTrigger: {
    trigger: ".video-section",
    start: "top bottom",
    end: "top top",
    scrub: true,
  },
});

// Video zoom effect (keep existing)
gsap.to(".scroll-video", {
  scale: 1,
  scrollTrigger: {
    trigger: ".video-section",
    start: "top bottom",
    end: "top top",
    scrub: true,
  },
});

// Smooth transition between sections (keep existing)
gsap.to(".container1", {
  opacity: 0,
  scrollTrigger: {
    trigger: ".video-section",
    start: "center center",
    end: "bottom top",
    scrub: true,
  },
});

gsap.from(".txtonvid", {
  opacity: 0,
  y: 60,
  duration: 1.2,
  delay: 0.4,
  scrollTrigger: {
    trigger: ".video-section",
    start: "top bottom",
    end: "top top",
    scrub: true,
  },
});

gsap.from(".txtSport", {
  opacity: 0,
  y: 50,
  duration: 1.2,
  delay: 0.4,
  scrollTrigger: {
    trigger: ".txtSport",
    start: "top 80%",
    end: "top 50%",
    toggleActions: "play none none",
  },
});

// 570s (keep existing)
gsap.to(".leftimg", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".txtSport",
    start: "top 50%",
    end: "top 20%",
    scrub: 1,
    markers: false,
  },
});

gsap.to(".rightimg", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".txtSport",
    start: "top 50%",
    end: "top 20%",
    scrub: 1,
    markers: false,
  },
});

//540c (keep existing)
gsap.to(".leftimg2", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".sideimgcon2",
    start: "top 57%",
    end: "top 20%",
    scrub: 1,
    markers: false,
  },
});

gsap.to(".rightimg2", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".sideimgcon2",
    start: "top 57%",
    end: "top 20%",
    scrub: 1,
    markers: false,
  },
});

//600lt (keep existing)
gsap.to(".leftimg3", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".sideimgcon3",
    start: "top 57%",
    end: "top 20%",
    scrub: 1,
    markers: false,
  },
});

gsap.to(".rightimg3", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".sideimgcon3",
    start: "top 57%",
    end: "top 20%",
    scrub: 1,
    markers: false,
  },
});

gsap.to(".scroll-video2", {
  scale: 1,
  scrollTrigger: {
    trigger: ".video-section2",
    start: "top 60%",
    end: "top top",
    scrub: true,
    markers: false,
  },
});

gsap.to(".container3", {
  scale: -1,
  scrollTrigger: {
    trigger: ".video-section",
    start: "top bottom",
    end: "top top",
    scrub: true,
  },
});

gsap.from(".txtSuper", {
  opacity: 0,
  y: 50,
  scrollTrigger: {
    trigger: ".txtSuper",
    start: "top 80%",
    end: "top 50%",
    toggleActions: "play none none",
  },
});

// Super Series animations (keep existing)
gsap.to(".super-leftimg", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".txtSuper",
    start: "top 50%",
    end: "top 20%",
    scrub: 1,
    markers: false,
  },
});

gsap.to(".super-rightimg", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".txtSuper",
    start: "top 50%",
    end: "top 20%",
    scrub: 1,
    markers: false,
  },
});

// 765LT animations (keep existing)
gsap.to(".super-leftimg2", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".super-sideimgcon",
    start: "bottom 70%",
    end: "bottom 30%",
    scrub: 1,
    markers: false,
  },
});

gsap.to(".super-rightimg2", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".super-sideimgcon",
    start: "bottom 70%",
    end: "bottom 30%",
    scrub: 1,
    markers: false,
  },
});

// 675LT spider animations (keep existing)
gsap.to(".super-leftimg3", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".super-sideimgcon2",
    start: "bottom 70%",
    end: "bottom 30%",
    scrub: 1,
    markers: false,
  },
});

gsap.to(".super-rightimg3", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".super-sideimgcon2",
    start: "bottom 70%",
    end: "bottom 30%",
    scrub: 1,
    markers: false,
  },
});

gsap.to(".scroll-video3", {
  scale: 1,
  scrollTrigger: {
    trigger: ".video-section3",
    start: "top 60%",
    end: "top top",
    scrub: true,
    markers: false,
  },
});

gsap.to(".container6", {
  opacity: 0,
  scrollTrigger: {
    trigger: ".video-section3",
    start: "center center",
    end: "bottom top",
    scrub: true,
  },
});

gsap.from(".txtUltimate", {
  opacity: 0,
  y: 50,
  duration: 1.2,
  delay: 0.4,
  scrollTrigger: {
    trigger: ".txtUltimate",
    start: "top 80%",
    end: "top 50%",
    toggleActions: "play none none",
  },
});

// P1 Image Animation (Left Side) (keep existing)
gsap.to(".ulti-leftimg", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".txtUltimate",
    start: "top 50%",
    end: "top 20%",
    scrub: 1,
    markers: false,
  },
});

// P1 Text Animation (Right Side) (keep existing)
gsap.to(".ulti-rightimg", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".txtUltimate",
    start: "top 50%",
    end: "top 20%",
    scrub: 1,
    markers: false,
  },
});

// Senna Image Animation (Left Side) (keep existing)
gsap.to(".ulti-leftimg2", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".ulti-sideimgcon",
    start: "bottom 70%",
    end: "bottom 30%",
    scrub: 1,
    markers: false,
  },
});

// Senna Text Animation (Right Side) (keep existing)
gsap.to(".ulti-rightimg2", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".ulti-sideimgcon",
    start: "bottom 70%",
    end: "bottom 30%",
    scrub: 1,
    markers: false,
  },
});

// Speedtail Image Animation (Left Side) (keep existing)
gsap.to(".ulti-leftimg3", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".ulti-sideimgcon2",
    start: "bottom 70%",
    end: "bottom 30%",
    scrub: 1,
    markers: false,
  },
});

// Speedtail Text Animation (Right Side) (keep existing)
gsap.to(".ulti-rightimg3", {
  opacity: 1,
  x: 0,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".ulti-sideimgcon2",
    start: "bottom 70%",
    end: "bottom 30%",
    scrub: 1,
    markers: false,
  },
});

// Footer animations (keep existing)
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

// Hover effects (keep existing)
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

// --- Review Modal and Form Logic (NEW/MODIFIED) ---

// Function to open the review modal
function openReviewModal() {
  const reviewModal = document.getElementById("reviewModal");
  reviewModal.classList.add("show");
  // If there's a message from a previous submission, hide it when opening the modal
  const reviewMessageDiv = reviewModal.querySelector(".review-message");
  if (reviewMessageDiv) {
    reviewMessageDiv.style.display = "none";
  }
}

// Function to close the review modal
function closeReviewModal() {
  const reviewModal = document.getElementById("reviewModal");
  reviewModal.classList.remove("show");
  // Optionally reset the form when closing
  const reviewForm = document.getElementById("reviewForm");
  if (reviewForm) {
    reviewForm.reset();
    selectedRating = 0; // Reset selected rating
    updateStarRating(); // Clear star visuals
    updateCharCounts(); // Clear char counts
  }
  // Remove any previous success/error message display
  const reviewMessageDiv = reviewModal.querySelector(".review-message");
  if (reviewMessageDiv) {
    reviewMessageDiv.style.display = "none";
  }
}

// Global variable to store the selected rating
let selectedRating = 0;

// Function to update star visuals based on selectedRating
function updateStarRating() {
  const stars = document.querySelectorAll("#starRating .star");
  const ratingInput = document.getElementById("ratingInput");

  stars.forEach((star) => {
    if (parseInt(star.dataset.value) <= selectedRating) {
      star.classList.add("active");
    } else {
      star.classList.remove("active");
    }
  });
  ratingInput.value = selectedRating; // Update hidden input
}

// Function to update character counts
function updateCharCounts() {
  const titleInput = document.getElementById("review_title");
  const titleCharCount = document.getElementById("titleCharCount");
  if (titleInput && titleCharCount) {
    titleCharCount.textContent = `${titleInput.value.length}/50`;
  }

  const textArea = document.getElementById("review_text_area");
  const textCharCount = document.getElementById("textCharCount");
  if (textArea && textCharCount) {
    textCharCount.textContent = `${textArea.value.length}/500`;
  }
}

// Event listener for DOM content loaded
document.addEventListener("DOMContentLoaded", () => {
  const stars = document.querySelectorAll("#starRating .star");
  const reviewTitle = document.getElementById("review_title");
  const reviewTextArea = document.getElementById("review_text_area");
  const reviewMessageDiv = document.querySelector(
    "#reviewModal .review-message"
  );

  // Add event listeners for star rating
  stars.forEach((star) => {
    star.addEventListener("click", () => {
      selectedRating = parseInt(star.dataset.value);
      updateStarRating();
    });
    // Add hover effect for stars
    star.addEventListener("mouseover", () => {
      stars.forEach((s) => {
        if (parseInt(s.dataset.value) <= parseInt(star.dataset.value)) {
          s.classList.add("active");
        } else {
          s.classList.remove("active");
        }
      });
    });
    star.addEventListener("mouseout", () => {
      updateStarRating(); // Revert to selected state on mouse out
    });
  });

  // Add event listeners for character count
  if (reviewTitle) {
    reviewTitle.addEventListener("input", updateCharCounts);
  }
  if (reviewTextArea) {
    reviewTextArea.addEventListener("input", updateCharCounts);
  }

  // Initial update for char counts (in case of pre-filled values or on page load)
  updateCharCounts();

  // Check if there's a review message to display from PHP after reload
  if (reviewMessageDiv && reviewMessageDiv.textContent.trim() !== "") {
    reviewMessageDiv.style.display = "block"; // Show the message
    openReviewModal(); // Open the modal to show the message
    // Hide the message after a few seconds
    setTimeout(() => {
      reviewMessageDiv.style.display = "none";
      closeReviewModal(); // Close modal after message disappears
    }, 5000); // Message visible for 5 seconds
  }
});
