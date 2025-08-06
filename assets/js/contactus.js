// GSAP is already loaded via CDN in the HTML file
// ScrollTrigger is also loaded via CDN
const gsap = window.gsap
const ScrollTrigger = window.ScrollTrigger

gsap.registerPlugin(ScrollTrigger)

// Initial animations
gsap.from("nav", { opacity: 0, y: -50, duration: 1 })

// Background text animation
gsap.from(".contact-bg-text", {
  opacity: 0,
  scale: 0.8,
  duration: 2,
  delay: 0.3,
  ease: "power2.out",
})

// Geometric elements animations
gsap.from(".geo-line", {
  scaleX: 0,
  duration: 1.5,
  delay: 0.5,
  stagger: 0.2,
  ease: "power2.out",
})

gsap.from(".geo-circle", {
  scale: 0,
  opacity: 0,
  duration: 1.5,
  delay: 0.8,
  stagger: 0.3,
  ease: "back.out(1.7)",
})

// Blurred circles animations
gsap.from(".blur-circle", {
  scale: 0,
  opacity: 0,
  duration: 2,
  delay: 0.6,
  ease: "power2.out",
})

gsap.from(".blur-circle-2", {
  scale: 0,
  opacity: 0,
  duration: 2,
  delay: 0.8,
  ease: "power2.out",
})

gsap.from(".blur-circle-3", {
  scale: 0,
  opacity: 0,
  duration: 2,
  delay: 1,
  ease: "power2.out",
})

// Contact badge animation
gsap.from(".contact-badge", {
  opacity: 0,
  y: 30,
  duration: 1,
  delay: 0.4,
})

// Contact title animation
gsap.from(".contact-title", {
  opacity: 0,
  y: 60,
  duration: 1.2,
  delay: 0.6,
})

// Contact subtitle animation
gsap.from(".contact-subtitle", {
  opacity: 0,
  y: 40,
  duration: 1,
  delay: 0.8,
})

gsap.utils.toArray(".contact-card").forEach((card, index) => {
  gsap.from(card, {
    opacity: 0,
    x: -50,
    duration: 0.8,
    delay: 0.5 + index * 0.15, // Staggered delay
    ease: "power2.out",
    immediateRender: false
  });
});

// Contact form animation
gsap.from(".contact-form", {
  opacity: 0,
  x: 50,
  duration: 1.2,
  delay: 0.8,
  ease: "power2.out",
})

// Form elements animations
gsap.from(".form-group", {
  opacity: 0,
  y: 30,
  duration: 0.8,
  delay: 1.2,
  stagger: 0.1,
  ease: "power2.out",
})

// Navbar hide/show on scroll
let lastScroll = 0
const navbar = document.querySelector(".navbar")

window.addEventListener("scroll", () => {
  const currentScroll = window.pageYOffset

  if (currentScroll > lastScroll && currentScroll > 100) {
    gsap.to(navbar, { y: -100, opacity: 0, duration: 0.3, ease: "power2.out" })
  } else {
    gsap.to(navbar, { y: 0, opacity: 1, duration: 0.3, ease: "power2.out" })
  }

  lastScroll = currentScroll
})

gsap.utils.toArray(".info-item").forEach((item, i) => {
  gsap.from(item, {
    opacity: 0,
    y: 50,
    duration: 0.8,
    ease: "power2.out",
    scrollTrigger: {
      trigger: ".additional-info",
      start: "top 80%",
      end: "top 30%",
      toggleActions: "play none none none",
      markers: false
    },
    delay: i * 0.1 // Stagger effect
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
})

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
})

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
})

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
})

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
})

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
})

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
})

// Interactive hover effects
document.querySelectorAll(".contact-card").forEach((card) => {
  card.addEventListener("mouseenter", () => {
    gsap.to(card, { scale: 1.02, duration: 0.3, ease: "power2.out" })
    gsap.to(card.querySelector(".card-icon"), {
      scale: 1.1,
      duration: 0.3,
      ease: "power2.out",
    })
  })

  card.addEventListener("mouseleave", () => {
    gsap.to(card, { scale: 1, duration: 0.3, ease: "power2.out" })
    gsap.to(card.querySelector(".card-icon"), {
      scale: 1,
      duration: 0.3,
      ease: "power2.out",
    })
  })
})

document.querySelectorAll(".info-item").forEach((item) => {
  item.addEventListener("mouseenter", () => {
    gsap.to(item, { scale: 1.02, duration: 0.3, ease: "power2.out" })
  })

  item.addEventListener("mouseleave", () => {
    gsap.to(item, { scale: 1, duration: 0.3, ease: "power2.out" })
  })
})

// Form interactions
document.querySelectorAll(".form-group input, .form-group select, .form-group textarea").forEach((input) => {
  input.addEventListener("focus", () => {
    gsap.to(input.parentElement, {
      scale: 1.02,
      duration: 0.3,
      ease: "power2.out",
    })
  })

  input.addEventListener("blur", () => {
    gsap.to(input.parentElement, {
      scale: 1,
      duration: 0.3,
      ease: "power2.out",
    })
  })
})

// Submit button animation
const submitBtn = document.querySelector(".submit-btn")
if (submitBtn) {
  submitBtn.addEventListener("mouseenter", () => {
    gsap.to(submitBtn, {
      scale: 1.02,
      duration: 0.3,
      ease: "power2.out",
    })
  })

  submitBtn.addEventListener("mouseleave", () => {
    gsap.to(submitBtn, {
      scale: 1,
      duration: 0.3,
      ease: "power2.out",
    })
  })
}

// Footer hover effects
document.querySelectorAll(".footer-nav a").forEach((link) => {
  link.addEventListener("mouseenter", () => {
    gsap.to(link, { scale: 1.05, duration: 0.3, ease: "power2.out" })
  })

  link.addEventListener("mouseleave", () => {
    gsap.to(link, { scale: 1, duration: 0.3, ease: "power2.out" })
  })
})

document.querySelectorAll(".footer-social a").forEach((link) => {
  link.addEventListener("mouseenter", () => {
    gsap.to(link, { x: 5, duration: 0.3, ease: "power2.out" })
  })

  link.addEventListener("mouseleave", () => {
    gsap.to(link, { x: 0, duration: 0.3, ease: "power2.out" })
  })
})

const footerBtn = document.querySelector(".footer-btn")
if (footerBtn) {
  footerBtn.addEventListener("mouseenter", () => {
    gsap.to(footerBtn, {
      scale: 1.1,
      duration: 0.3,
      ease: "power2.out",
      boxShadow: "0 10px 30px rgba(255, 255, 255, 0.2)",
    })
  })

  footerBtn.addEventListener("mouseleave", () => {
    gsap.to(footerBtn, {
      scale: 1,
      duration: 0.3,
      ease: "power2.out",
      boxShadow: "none",
    })
  })
}

// Form submission handling
const contactForm = document.getElementById("contactForm")
if (contactForm) {
  contactForm.addEventListener("submit", (e) => {
    e.preventDefault()

    // Animate submit button
    gsap.to(submitBtn, {
      scale: 0.95,
      duration: 0.1,
      yoyo: true,
      repeat: 1,
      ease: "power2.inOut",
    })

    // Show success message (you can customize this)
    setTimeout(() => {
      alert("Thank you for your message! We'll get back to you soon.")
      contactForm.reset()
    }, 300)
  })
}

// Parallax effect for background elements
gsap.to(".geo-line-1", {
  x: 100,
  scrollTrigger: {
    trigger: ".contact-main",
    start: "top bottom",
    end: "bottom top",
    scrub: 1,
  },
})

gsap.to(".geo-line-2", {
  x: -80,
  scrollTrigger: {
    trigger: ".contact-main",
    start: "top bottom",
    end: "bottom top",
    scrub: 1,
  },
})

gsap.to(".geo-circle-1", {
  y: -50,
  rotation: 180,
  scrollTrigger: {
    trigger: ".contact-main",
    start: "top bottom",
    end: "bottom top",
    scrub: 1,
  },
})

gsap.to(".geo-circle-2", {
  y: 30,
  rotation: -90,
  scrollTrigger: {
    trigger: ".contact-main",
    start: "top bottom",
    end: "bottom top",
    scrub: 1,
  },
})

// Add parallax effect to blurred circles
gsap.to(".blur-circle", {
  y: -30,
  x: 20,
  scrollTrigger: {
    trigger: ".contact-main",
    start: "top bottom",
    end: "bottom top",
    scrub: 1,
  },
})

gsap.to(".blur-circle-2", {
  y: 40,
  x: -15,
  scrollTrigger: {
    trigger: ".contact-main",
    start: "top bottom",
    end: "bottom top",
    scrub: 1,
  },
})

gsap.to(".blur-circle-3", {
  y: -25,
  x: 10,
  scrollTrigger: {
    trigger: ".contact-main",
    start: "top bottom",
    end: "bottom top",
    scrub: 1,
  },
})
