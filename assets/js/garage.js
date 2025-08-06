// GSAP is already loaded via CDN in the HTML file
// ScrollTrigger is also loaded via CDN
const gsap = window.gsap
const ScrollTrigger = window.ScrollTrigger

gsap.registerPlugin(ScrollTrigger)

// Initial animations
gsap.from("nav", { opacity: 0, y: -50, duration: 1 })

// Hero section animations
gsap.from(".garage-title", {
  opacity: 0,
  y: 60,
  duration: 1.2,
  delay: 0.4,
})

gsap.from(".garage-subtitle", {
  opacity: 0,
  y: 40,
  duration: 1.2,
  delay: 0.6,
})

// Collection container animation
gsap.from(".collection-container", {
  opacity: 0,
  y: 50,
  duration: 1.2,
  delay: 0.8,
})

// Car items stagger animation
gsap.from(".car-item", {
  opacity: 0,
  x: -50,
  duration: 0.8,
  delay: 1,
  stagger: 0.2,
})

// Order summary animation
gsap.from(".order-summary", {
  opacity: 0,
  x: 50,
  duration: 1.2,
  delay: 1.2,
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

// Garage functionality
const processingFee = 2500

function formatPrice(price) {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price)
}

function updateSummary() {
  const carItems = document.querySelectorAll(".car-item")
  const carCount = carItems.length

  let subtotal = 0
  carItems.forEach((item) => {
    const price = Number.parseInt(item.dataset.price)
    subtotal += price
  })

  const total = subtotal + processingFee

  // Update counts
  document.getElementById("carCount").textContent = carCount
  document.getElementById("summaryCarCount").textContent = carCount

  // Update amounts
  document.getElementById("subtotalAmount").textContent = formatPrice(subtotal)
  document.getElementById("totalAmount").textContent = formatPrice(total)

  // Update financing options
  document.getElementById("financing60").textContent = formatPrice(Math.round(total * 0.0185)) + "/mo"
  document.getElementById("financing72").textContent = formatPrice(Math.round(total * 0.0158)) + "/mo"
  document.getElementById("financing84").textContent = formatPrice(Math.round(total * 0.0138)) + "/mo"

  // Show/hide empty state
  const carsList = document.getElementById("carsList")
  const emptyState = document.getElementById("emptyState")
  const orderSummary = document.getElementById("orderSummary")

  if (carCount === 0) {
    carsList.style.display = "none"
    emptyState.style.display = "block"
    orderSummary.style.display = "none"

    // Animate empty state
    gsap.from(emptyState, {
      opacity: 0,
      y: 50,
      duration: 0.8,
      ease: "power2.out",
    })
  } else {
    carsList.style.display = "block"
    emptyState.style.display = "none"
    orderSummary.style.display = "block"
  }
}

function removeCar(carId) {
  const carItem = document.querySelector(`[data-car-id="${carId}"]`)

  if (carItem) {
    // Animate removal
    gsap.to(carItem, {
      opacity: 0,
      x: -100,
      duration: 0.5,
      ease: "power2.out",
      onComplete: () => {
        carItem.remove()
        updateSummary()

        // Re-animate remaining items
        gsap.from(".car-item", {
          opacity: 0,
          x: -20,
          duration: 0.5,
          stagger: 0.1,
        })
      },
    })
  }
}

function clearAllCars() {
  const carItems = document.querySelectorAll(".car-item")

  // Animate all items out
  gsap.to(carItems, {
    opacity: 0,
    x: -100,
    duration: 0.5,
    stagger: 0.1,
    ease: "power2.out",
    onComplete: () => {
      carItems.forEach((item) => item.remove())
      updateSummary()
    },
  })
}

function proceedToConsultation() {
  // Add your consultation logic here
  alert("Proceeding to consultation booking...")
}

function requestCustomQuote() {
  // Add your custom quote logic here
  alert("Requesting custom quote...")
}

// Hover effects for interactive elements
document.querySelectorAll(".car-item").forEach((item) => {
  item.addEventListener("mouseenter", () => {
    gsap.to(item, { scale: 1.02, duration: 0.3, ease: "power2.out" })
  })

  item.addEventListener("mouseleave", () => {
    gsap.to(item, { scale: 1, duration: 0.3, ease: "power2.out" })
  })
})

document.querySelectorAll(".remove-btn").forEach((btn) => {
  btn.addEventListener("mouseenter", () => {
    gsap.to(btn, { scale: 1.05, duration: 0.3, ease: "power2.out" })
  })

  btn.addEventListener("mouseleave", () => {
    gsap.to(btn, { scale: 1, duration: 0.3, ease: "power2.out" })
  })
})

document.querySelectorAll(".primary-action-btn, .secondary-action-btn").forEach((btn) => {
  btn.addEventListener("mouseenter", () => {
    gsap.to(btn, { scale: 1.02, duration: 0.3, ease: "power2.out" })
  })

  btn.addEventListener("mouseleave", () => {
    gsap.to(btn, { scale: 1, duration: 0.3, ease: "power2.out" })
  })
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

// Initialize summary on page load
document.addEventListener("DOMContentLoaded", () => {
  updateSummary()
})
