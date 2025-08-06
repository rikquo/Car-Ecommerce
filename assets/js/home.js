

gsap.from("nav", { opacity: 0, y: -50, duration: 1 })
gsap.from(".rev-title", { opacity: 0, y: 60, duration: 1.2, delay: 0.4 })
gsap.from(".rev", { opacity: 0, y: 60, duration: 1.2, delay: 0.6 })
gsap.from(".image", { opacity: 0, y: 50, duration: 1.2, delay: 0.2 })

let lastScroll = 0
const navbar = document.querySelector(".nav")

window.addEventListener("scroll", () => {
  const currentScroll = window.pageYOffset

  if (currentScroll > lastScroll && currentScroll > 100) {
    gsap.to(navbar, { y: -100, opacity: 0, duration: 0.1, ease: "power2.out" })
  } else {
    gsap.to(navbar, { y: 0, opacity: 1, duration: 0.1, ease: "power2.out" })
  }

  lastScroll = currentScroll
})


// ===== SMOOTH SCROLLING FOR NAVIGATION =====
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault()
    const target = document.querySelector(this.getAttribute("href"))

    if (target) {
      const offsetTop = target.offsetTop - 100 // Account for fixed navbar

      window.scrollTo({
        top: offsetTop,
        behavior: "smooth",
      })
    }
  })
})

// Hero section zoom effect
gsap.to(".hero-wrapper", {
  scale: 1.5,
  scrollTrigger: {
    trigger: ".video-section",
    start: "top bottom",
    end: "top top",
    scrub: true,
  },
})

// Video zoom effect
gsap.to(".scroll-video", {
  scale: 1,
  scrollTrigger: {
    trigger: ".video-section",
    start: "top bottom",
    end: "top top",
    scrub: true,
  },
})

// Smooth transition between sections
gsap.to(".container1", {
  opacity: 0,
  scrollTrigger: {
    trigger: ".video-section",
    start: "center center",
    end: "bottom top",
    scrub: true,
  },
})

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
})

gsap.from(".txtSport", {
  opacity: 0,
  y: 50,
  scrollTrigger: {
    trigger: ".txtSport",
    start: "top 80%",
    end: "top 50%",
    toggleActions: "play none none",
  },
})

// 570s
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
})

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
})

//540c
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
})

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
})

//600lt
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
})

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
})

gsap.to(".scroll-video2", {
  scale: 1,
  scrollTrigger: {
    trigger: ".video-section2",
    start: "top 60%",
    end: "top top",
    scrub: true,
    markers: false,
  },
})

gsap.to(".container3", {
  scale: -1,
  scrollTrigger: {
    trigger: ".video-section",
    start: "top bottom",
    end: "top top",
    scrub: true,
  },
})

gsap.from(".txtSuper", {
  opacity: 0,
  y: 50,
  scrollTrigger: {
    trigger: ".txtSuper",
    start: "top 80%",
    end: "top 50%",
    toggleActions: "play none none",
  },
})

// Super Series animations
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
})

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
})

// 765LT animations
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
})

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
})

// 675LT spider animations
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
})

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
})

gsap.to(".scroll-video3", {
  scale: 1,
  scrollTrigger: {
    trigger: ".video-section3",
    start: "top 60%",
    end: "top top",
    scrub: true,
    markers: false,
  },
})

gsap.to(".container6", {
  opacity: 0,
  scrollTrigger: {
    trigger: ".video-section3",
    start: "center center",
    end: "bottom top",
    scrub: true,
  },
})

gsap.from(".txtUltimate", {
  opacity: 0,
  y: 50,
  scrollTrigger: {
    trigger: ".txtUltimate",
    start: "top 80%",
    end: "top 50%",
    toggleActions: "play none none",
  },
})

// P1 Image Animation (Left Side)
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
})

// P1 Text Animation (Right Side)
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
})

// Senna Image Animation (Left Side)
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
})

// Senna Text Animation (Right Side)
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
})

// Speedtail Image Animation (Left Side)
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
})

// Speedtail Text Animation (Right Side)
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

// Hover effects
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
