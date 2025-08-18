// const gsap = window.gsap;
// const ScrollTrigger = window.ScrollTrigger;

// gsap.registerPlugin(ScrollTrigger);

// // Initial animations
// gsap.from("nav", { opacity: 0, y: -50, duration: 1 });

// // Hero section animations
// gsap.from(".garage-title", {
//   opacity: 0,
//   y: 60,
//   duration: 1.2,
//   delay: 0.4,
// });

// gsap.from(".garage-subtitle", {
//   opacity: 0,
//   y: 40,
//   duration: 1.2,
//   delay: 0.6,
// });

// // Collection container animation
// gsap.from(".collection-container", {
//   opacity: 0,
//   y: 50,
//   duration: 1.2,
//   delay: 0.8,
// });

// // Car items stagger animation
// gsap.from(".car-item", {
//   opacity: 0,
//   x: -50,
//   duration: 0.8,
//   delay: 1,
//   stagger: 0.2,
// });

// // Order summary animation
// gsap.from(".order-summary", {
//   opacity: 0,
//   x: 50,
//   duration: 1.2,
//   delay: 1.2,
// });

// // Navbar hide/show on scroll
// let lastScroll = 0;
// const navbar = document.querySelector(".navbar");

// window.addEventListener("scroll", () => {
//   const currentScroll = window.pageYOffset;

//   if (currentScroll > lastScroll && currentScroll > 100) {
//     gsap.to(navbar, { y: -100, opacity: 0, duration: 0.3, ease: "power2.out" });
//   } else {
//     gsap.to(navbar, { y: 0, opacity: 1, duration: 0.3, ease: "power2.out" });
//   }

//   lastScroll = currentScroll;
// });
