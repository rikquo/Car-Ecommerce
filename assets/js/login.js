// --- GSAP and Animations ---
const gsap = window.gsap;
const ScrollTrigger = window.ScrollTrigger;
gsap.registerPlugin(ScrollTrigger);

// Initial animations
gsap.from("nav", { opacity: 0, y: -50, duration: 1 });

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

// Auth card animation
gsap.from(".auth-card", {
  opacity: 0,
  y: 50,
  scale: 0.9,
  duration: 1,
  ease: "power2.out",
  delay: 0.3,
});

// Form elements stagger animation
gsap.from(".auth-header > *", {
  opacity: 0,
  y: 30,
  duration: 0.8,
  stagger: 0.2,
  delay: 0.8,
});

gsap.from(".form-group", {
  opacity: 0,
  x: -30,
  duration: 0.6,
  stagger: 0.1,
  delay: 1,
});

gsap.from(".auth-switch", {
  opacity: 0,
  y: 20,
  duration: 0.6,
  delay: 1.6,
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

// --- Core Functionality ---
let isLoginForm = true;

// Function to switch between login and signup forms
function switchForm() {
  const loginForm = document.getElementById("loginForm");
  const signupForm = document.getElementById("signupForm");
  const authTitle = document.querySelector(".auth-title");
  const authSubtitle = document.querySelector(".auth-subtitle");
  const switchText = document.querySelector(".switch-text");
  const switchBtn = document.querySelector(".switch-btn");

  // Animate out current form
  gsap.to(isLoginForm ? loginForm : signupForm, {
    opacity: 0,
    x: -30,
    duration: 0.3,
    ease: "power2.out",
    onComplete: () => {
      // Hide current form and show new form
      if (isLoginForm) {
        loginForm.classList.add("hidden");
        signupForm.classList.remove("hidden");
        authTitle.textContent = "Create Account";
        authSubtitle.textContent = "Join the Rev Garage community";
        switchText.textContent = "Already have an account?";
        switchBtn.textContent = "Sign In";
      } else {
        signupForm.classList.add("hidden");
        loginForm.classList.remove("hidden");
        authTitle.textContent = "Welcome Back";
        authSubtitle.textContent = "Sign in to your Rev Garage account";
        switchText.textContent = "Don't have an account?";
        switchBtn.textContent = "Sign Up";
      }

      // Animate in new form
      gsap.fromTo(
        isLoginForm ? signupForm : loginForm,
        { opacity: 0, x: 30 },
        { opacity: 1, x: 0, duration: 0.3, ease: "power2.out" }
      );

      // Animate title change
      gsap.fromTo(
        [authTitle, authSubtitle],
        { opacity: 0, y: -10 },
        { opacity: 1, y: 0, duration: 0.4, stagger: 0.1, delay: 0.1 }
      );

      isLoginForm = !isLoginForm;
    },
  });
}

function togglePassword(passwordId, toggleButton) {
  const passwordInput = document.getElementById(passwordId);
  const isPassword = passwordInput.type === "password";

  passwordInput.type = isPassword ? "text" : "password";
  toggleButton.classList.toggle("show-password", isPassword);

  gsap.fromTo(
    toggleButton,
    { scale: 0.8 },
    { scale: 1, duration: 0.2, ease: "back.out(1.7)" }
  );
}

function showMessage(message, type) {
  const existingMessages = document.querySelectorAll(
    ".error-message, .success-message"
  );
  existingMessages.forEach((msg) => msg.remove());

  const messageDiv = document.createElement("div");
  messageDiv.className =
    type === "success" ? "success-message" : "error-message";
  messageDiv.textContent = message;

  const authCard = document.querySelector(".auth-card");
  const authHeader = document.querySelector(".auth-header");
  authCard.insertBefore(messageDiv, authHeader.nextSibling);

  gsap.fromTo(
    messageDiv,
    { opacity: 0, y: -20 },
    { opacity: 1, y: 0, duration: 0.5, ease: "power2.out" }
  );

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

function handleRememberMe() {
  const rememberCheckbox = document.getElementById("rememberMe");
  const checkboxLabel = document.querySelector('label[for="rememberMe"]');

  if (rememberCheckbox && checkboxLabel) {
    rememberCheckbox.addEventListener("change", function () {
      if (this.checked) {
        checkboxLabel.textContent = "Remember me for 30 days";
        gsap.to(checkboxLabel, { color: "#4caf50", duration: 0.3 });
      } else {
        checkboxLabel.textContent = "Remember me";
        gsap.to(checkboxLabel, {
          color: "rgba(255, 255, 255, 0.7)",
          duration: 0.3,
        });
      }
    });
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  const signupForm = document.getElementById("signupForm");

  handleRememberMe();

  document.querySelectorAll(".form-input").forEach((input) => {
    input.addEventListener("focus", () => {
      gsap.to(input, { scale: 1.02, duration: 0.3, ease: "power2.out" });
    });
    input.addEventListener("blur", () => {
      gsap.to(input, { scale: 1, duration: 0.3, ease: "power2.out" });
    });
  });

  document
    .querySelectorAll(".auth-submit, .switch-btn, .social-btn")
    .forEach((btn) => {
      btn.addEventListener("mouseenter", () => {
        gsap.to(btn, { scale: 1.02, duration: 0.3, ease: "power2.out" });
      });
      btn.addEventListener("mouseleave", () => {
        gsap.to(btn, { scale: 1, duration: 0.3, ease: "power2.out" });
      });
    });

  if (loginForm) {
    loginForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      const submitBtn = loginForm.querySelector(".auth-submit");
      const originalText = submitBtn.textContent;

      submitBtn.textContent = "Signing In...";
      submitBtn.classList.add("loading");

      const formData = new FormData(loginForm);
      const data = Object.fromEntries(formData.entries());
      data.action = "login";

      const rememberCheckbox = document.getElementById("rememberMe");
      data.rememberMe = rememberCheckbox && rememberCheckbox.checked;

      try {
        const response = await fetch("login.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
        });
        const result = await response.json();

        if (result.success) {
          showMessage(result.message, "success");

          if (data.rememberMe) {
            setTimeout(() => {
              showMessage("You'll stay logged in for 30 days!", "success");
            }, 1000);
          }

          setTimeout(() => {
            window.location.href = result.redirect || "index.php";
          }, 2000);
        } else {
          showMessage(result.message, "error");
        }
      } catch (error) {
        showMessage("An unexpected error occurred. Please try again.", "error");
        console.error("Login Error:", error);
      } finally {
        submitBtn.textContent = originalText;
        submitBtn.classList.remove("loading");
      }
    });
  }

  if (signupForm) {
    signupForm.addEventListener("submit", async (event) => {
      event.preventDefault();
      const submitBtn = signupForm.querySelector(".auth-submit");
      const originalText = submitBtn.textContent;

      submitBtn.textContent = "Creating Account...";
      submitBtn.classList.add("loading");

      const formData = new FormData(signupForm);
      const data = Object.fromEntries(formData.entries());
      data.action = "signup";

      if (data.password !== data.confirmPassword) {
        showMessage("Passwords do not match.", "error");
        submitBtn.textContent = originalText;
        submitBtn.classList.remove("loading");
        return;
      }

      try {
        const response = await fetch("login.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
        });
        const result = await response.json();

        if (result.success) {
          showMessage(result.message, "success");
          setTimeout(() => {
            switchForm();
          }, 2000);
        } else {
          showMessage(result.message, "error");
        }
      } catch (error) {
        showMessage("An unexpected error occurred. Please try again.", "error");
        console.error("Signup Error:", error);
      } finally {
        submitBtn.textContent = originalText;
        submitBtn.classList.remove("loading");
      }
    });
  }

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
    input.addEventListener("input", () => {
      if (input.value && !validatePassword(input.value)) {
        input.style.borderColor = "rgba(244, 67, 54, 0.5)";
      } else {
        input.style.borderColor = "rgba(255, 255, 255, 0.1)";
      }
    });
  });
});

function handleGoogleLogin() {
  alert("Google login is not yet implemented.");
}

function handleFacebookLogin() {
  alert("Facebook login is not yet implemented.");
}
