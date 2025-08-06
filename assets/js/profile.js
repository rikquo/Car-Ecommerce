// GSAP is already loaded via CDN in the HTML file
const gsap = window.gsap;
const ScrollTrigger = window.ScrollTrigger;
gsap.registerPlugin(ScrollTrigger);

// Initial animations
gsap.from("nav", { opacity: 0, y: -50, duration: 1 });

// Background elements animations
gsap.from(".bg-circle", {
  scale: 0,
  opacity: 0,
  duration: 1.5,
  stagger: 0.3,
  ease: "back.out(1.7)",
  delay: 0.2,
});

gsap.from(".bg-line", {
  scaleX: 0,
  duration: 1.2,
  stagger: 0.2,
  ease: "power2.out",
  delay: 0.5,
});

// Profile header animation
gsap.from(".profile-header", {
  opacity: 0,
  y: 50,
  duration: 1,
  ease: "power2.out",
  delay: 0.3,
});

// Profile sections stagger animation
gsap.from(".profile-section", {
  opacity: 0,
  y: 40,
  duration: 0.8,
  stagger: 0.2,
  ease: "power2.out",
  delay: 0.6,
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

// Password toggle functionality
function togglePassword(passwordId, toggleButton) {
  const passwordInput = document.getElementById(passwordId);
  const isPassword = passwordInput.type === "password";
  
  passwordInput.type = isPassword ? "text" : "password";
  toggleButton.classList.toggle("show-password", isPassword);
  
  // Add a subtle animation
  gsap.fromTo(toggleButton, { scale: 0.8 }, { scale: 1, duration: 0.2, ease: "back.out(1.7)" });
}

// Message display function
function showMessage(message, type, formId) {
  // Remove existing messages
  const existingMessages = document.querySelectorAll(".message");
  existingMessages.forEach((msg) => msg.remove());

  // Create new message
  const messageDiv = document.createElement("div");
  messageDiv.className = `message ${type}`;
  messageDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>${message}`;

  // Insert at the top of the form
  const form = document.getElementById(formId);
  form.insertBefore(messageDiv, form.firstChild);

  // Animate in
  gsap.fromTo(messageDiv, { opacity: 0, y: -20 }, { opacity: 1, y: 0, duration: 0.5, ease: "power2.out" });

  // Auto remove after 5 seconds
  setTimeout(() => {
    gsap.to(messageDiv, {
      opacity: 0,
      y: -20,
      duration: 0.3,
      ease: "power2.out",
      onComplete: () => messageDiv.remove(),
    });
  }, 5000);
}

// Form submission handlers
document.addEventListener("DOMContentLoaded", () => {
  const profileForm = document.getElementById("profileForm");
  const passwordForm = document.getElementById("passwordForm");

  // Input focus animations
  document.querySelectorAll(".form-input").forEach((input) => {
    input.addEventListener("focus", () => {
      gsap.to(input, { scale: 1.02, duration: 0.3, ease: "power2.out" });
    });
    input.addEventListener("blur", () => {
      gsap.to(input, { scale: 1, duration: 0.3, ease: "power2.out" });
    });
  });

  // Button hover effects
  document.querySelectorAll(".btn-primary, .btn-secondary").forEach((btn) => {
    btn.addEventListener("mouseenter", () => {
      gsap.to(btn, { scale: 1.02, duration: 0.3, ease: "power2.out" });
    });
    btn.addEventListener("mouseleave", () => {
      gsap.to(btn, { scale: 1, duration: 0.3, ease: "power2.out" });
    });
  });

  // Action card hover effects
  document.querySelectorAll(".action-card").forEach((card) => {
    card.addEventListener("mouseenter", () => {
      gsap.to(card, { scale: 1.02, duration: 0.3, ease: "power2.out" });
      gsap.to(card.querySelector(".action-icon"), { scale: 1.1, duration: 0.3, ease: "power2.out" });
    });
    card.addEventListener("mouseleave", () => {
      gsap.to(card, { scale: 1, duration: 0.3, ease: "power2.out" });
      gsap.to(card.querySelector(".action-icon"), { scale: 1, duration: 0.3, ease: "power2.out" });
    });
  });

  // Profile form submission
  if (profileForm) {
    profileForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      const submitBtn = profileForm.querySelector(".btn-primary");
      const originalText = submitBtn.innerHTML;

      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      submitBtn.classList.add("loading");

      const formData = new FormData(profileForm);

      try {
        const response = await fetch("profile.php", {
          method: "POST",
          body: formData,
        });
        const result = await response.json();

        if (result.success) {
          showMessage(result.message, "success", "profileForm");
          // Update the profile name in the header if username changed
          const newUsername = formData.get('username');
          const profileName = document.querySelector('.profile-name');
          if (profileName && newUsername) {
            profileName.textContent = newUsername;
          }
        } else {
          showMessage(result.message, "error", "profileForm");
        }
      } catch (error) {
        showMessage("An unexpected error occurred. Please try again.", "error", "profileForm");
        console.error("Profile Update Error:", error);
      } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.classList.remove("loading");
      }
    });
  }

  // Password form submission
  if (passwordForm) {
    passwordForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      const submitBtn = passwordForm.querySelector(".btn-secondary");
      const originalText = submitBtn.innerHTML;

      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing...';
      submitBtn.classList.add("loading");

      const formData = new FormData(passwordForm);

      try {
        const response = await fetch("profile.php", {
          method: "POST",
          body: formData,
        });
        const result = await response.json();

        if (result.success) {
          showMessage(result.message, "success", "passwordForm");
          passwordForm.reset();
        } else {
          showMessage(result.message, "error", "passwordForm");
        }
      } catch (error) {
        showMessage("An unexpected error occurred. Please try again.", "error", "passwordForm");
        console.error("Password Change Error:", error);
      } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.classList.remove("loading");
      }
    });
  }

  // Avatar edit button animation
  const avatarEditBtn = document.querySelector('.avatar-edit-btn');
  if (avatarEditBtn) {
    avatarEditBtn.addEventListener('click', () => {
      gsap.to(avatarEditBtn, {
        scale: 0.9,
        duration: 0.1,
        yoyo: true,
        repeat: 1,
        ease: "power2.inOut",
      });
      // Here you would typically open a file picker or modal for avatar upload
      alert('Avatar upload functionality would be implemented here');
    });
  }

  // Real-time validation
  const emailInputs = document.querySelectorAll('input[type="email"]');
  const passwordInputs = document.querySelectorAll('input[type="password"]');

  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  function validatePassword(password) {
    return password.length >= 8;
  }

  emailInputs.forEach((input) => {
    input.addEventListener("blur", () => {
      if (input.value && !validateEmail(input.value)) {
        input.style.borderColor = "rgba(244, 67, 54, 0.5)";
      } else {
        input.style.borderColor = "rgba(255, 255, 255, 0.1)";
      }
    });
  });

  passwordInputs.forEach((input) => {
    if (input.id === 'new_password') {
      input.addEventListener("input", () => {
        if (input.value && !validatePassword(input.value)) {
          input.style.borderColor = "rgba(244, 67, 54, 0.5)";
        } else {
          input.style.borderColor = "rgba(255, 255, 255, 0.1)";
        }
      });
    }
  });

  // Password confirmation validation
  const confirmPasswordInput = document.getElementById('confirm_password');
  const newPasswordInput = document.getElementById('new_password');
  
  if (confirmPasswordInput && newPasswordInput) {
    confirmPasswordInput.addEventListener('input', () => {
      if (confirmPasswordInput.value && confirmPasswordInput.value !== newPasswordInput.value) {
        confirmPasswordInput.style.borderColor = "rgba(244, 67, 54, 0.5)";
      } else {
        confirmPasswordInput.style.borderColor = "rgba(255, 255, 255, 0.1)";
      }
    });
  }
});

// Parallax effect for background elements
gsap.to(".bg-circle-1", {
  y: -30,
  x: 20,
  scrollTrigger: {
    trigger: ".profile-main",
    start: "top bottom",
    end: "bottom top",
    scrub: 1,
  },
});

gsap.to(".bg-circle-2", {
  y: 40,
  x: -15,
  scrollTrigger: {
    trigger: ".profile-main",
    start: "top bottom",
    end: "bottom top",
    scrub: 1,
  },
});

gsap.to(".bg-circle-3", {
  y: -25,
  x: 10,
  scrollTrigger: {
    trigger: ".profile-main",
    start: "top bottom",
    end: "bottom top",
    scrub: 1,
  },
});