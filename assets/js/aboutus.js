// GSAP is already loaded via CDN in the HTML file
// ScrollTrigger is also loaded via CDN
const gsap = window.gsap
const ScrollTrigger = window.ScrollTrigger

gsap.registerPlugin(ScrollTrigger)

// Initial animations
gsap.from("nav", { opacity: 0, y: -50, duration: 1 })

// Hero section animations
gsap.from(".about-title", {
  opacity: 0,
  y: 60,
  duration: 1.2,
  delay: 0.4,
})

gsap.from(".about-subtitle", {
  opacity: 0,
  y: 40,
  duration: 1.2,
  delay: 0.6,
})

gsap.from(".breadcrumb", {
  opacity: 0,
  y: 20,
  duration: 1,
  delay: 0.8,
})

gsap.from(".hero-circle", {
  opacity: 0,
  scale: 0.8,
  duration: 1.5,
  delay: 0.5,
  ease: "back.out(1.7)",
})

gsap.from(".circle-stats .stat-item", {
  opacity: 0,
  x: 50,
  duration: 1,
  delay: 1.2,
  stagger: 0.2,
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

// Who We Are section animations
gsap.from(".who-we-are-left img", {
  opacity: 0,
  x: -100,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".who-we-are",
    start: "top 70%",
    end: "top 30%",
    toggleActions: "play none none reverse",
  },
})

gsap.from(".section-subtitle", {
  opacity: 0,
  y: 30,
  duration: 1,
  scrollTrigger: {
    trigger: ".who-we-are-right",
    start: "top 70%",
    toggleActions: "play none none reverse",
  },
})

gsap.from(".section-title", {
  opacity: 0,
  y: 40,
  duration: 1.2,
  delay: 0.2,
  scrollTrigger: {
    trigger: ".who-we-are-right",
    start: "top 70%",
    toggleActions: "play none none reverse",
  },
})

gsap.from(".section-description", {
  opacity: 0,
  y: 30,
  duration: 1,
  delay: 0.4,
  scrollTrigger: {
    trigger: ".who-we-are-right",
    start: "top 70%",
    toggleActions: "play none none reverse",
  },
})

gsap.from(".feature-item", {
  opacity: 0,
  x: 30,
  duration: 0.8,
  stagger: 0.1,
  delay: 0.6,
  scrollTrigger: {
    trigger: ".features-list",
    start: "top 80%",
    toggleActions: "play none none reverse",
  },
})

gsap.from(".cta-button", {
  opacity: 0,
  scale: 0.8,
  duration: 1,
  delay: 0.8,
  ease: "back.out(1.7)",
  scrollTrigger: {
    trigger: ".cta-button",
    start: "top 90%",
    toggleActions: "play none none reverse",
  },
})

// Stats section animations
gsap.from(".stats-left .stats-subtitle", {
  opacity: 0,
  y: 30,
  duration: 1,
  scrollTrigger: {
    trigger: ".stats-section",
    start: "top 70%",
    toggleActions: "play none none reverse",
  },
})

gsap.from(".stats-left .stats-title", {
  opacity: 0,
  y: 40,
  duration: 1.2,
  delay: 0.2,
  scrollTrigger: {
    trigger: ".stats-section",
    start: "top 70%",
    toggleActions: "play none none reverse",
  },
})

gsap.from(".stats-left .stats-description", {
  opacity: 0,
  y: 30,
  duration: 1,
  delay: 0.4,
  scrollTrigger: {
    trigger: ".stats-section",
    start: "top 70%",
    toggleActions: "play none none reverse",
  },
})

gsap.from(".stat-box", {
  opacity: 0,
  y: 50,
  duration: 1,
  stagger: 0.2,
  delay: 0.3,
  scrollTrigger: {
    trigger: ".stats-grid",
    start: "top 80%",
    toggleActions: "play none none reverse",
  },
})

// CTA section animations
gsap.from(".cta-title", {
  opacity: 0,
  y: 50,
  duration: 1.2,
  scrollTrigger: {
    trigger: ".cta-section",
    start: "top 70%",
    toggleActions: "play none none reverse",
  },
})

gsap.from(".cta-description", {
  opacity: 0,
  y: 30,
  duration: 1,
  delay: 0.3,
  scrollTrigger: {
    trigger: ".cta-section",
    start: "top 70%",
    toggleActions: "play none none reverse",
  },
})

gsap.from(".cta-main-button", {
  opacity: 0,
  scale: 0.8,
  duration: 1,
  delay: 0.6,
  ease: "back.out(1.7)",
  scrollTrigger: {
    trigger: ".cta-section",
    start: "top 70%",
    toggleActions: "play none none reverse",
  },
})

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

// Hover effects for interactive elements
document.querySelectorAll(".stat-box").forEach((box) => {
  box.addEventListener("mouseenter", () => {
    gsap.to(box, { scale: 1.05, duration: 0.3, ease: "power2.out" })
  })

  box.addEventListener("mouseleave", () => {
    gsap.to(box, { scale: 1, duration: 0.3, ease: "power2.out" })
  })
})

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

// Button hover effects
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
