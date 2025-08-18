// import { Chart } from "@/components/ui/chart"
// Admin Dashboard JavaScript
document.addEventListener("DOMContentLoaded", () => {
  // Initialize dashboard
  initializeDashboard();
  initializeCharts();
  initializeModals();
  initializeEventListeners();
});

// Dashboard Initialization
// Dashboard Initialization
function initializeDashboard() {
  const navLinks = document.querySelectorAll(".nav-link");
  const sections = document.querySelectorAll(".content-section");

  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      // Check if the link href is an internal anchor
      if (
        this.href.startsWith(
          window.location.origin + window.location.pathname + "#"
        )
      ) {
        e.preventDefault();

        // Remove active class from all nav items and sections
        navLinks.forEach((nav) => nav.parentElement.classList.remove("active"));
        sections.forEach((section) => section.classList.remove("active"));

        // Add active class to clicked nav item
        this.parentElement.classList.add("active");

        // Show corresponding section
        const sectionId = this.dataset.section + "-section";
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
          targetSection.classList.add("active");
          const pageTitle = document.querySelector(".page-title");
          pageTitle.textContent = this.querySelector("span").textContent;
        }
      }
    });
  });

  const sidebarToggle = document.querySelector(".sidebar-toggle");
  const sidebar = document.querySelector(".sidebar");

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("active");
    });
  }

  const themeToggle = document.querySelector(".theme-toggle");
  if (themeToggle) {
    themeToggle.addEventListener("click", function () {
      document.body.classList.toggle("light-theme");
      const icon = this.querySelector("i");
      icon.classList.toggle("fa-moon");
      icon.classList.toggle("fa-sun");
    });
  }
}

// Charts Initialization
function initializeCharts() {
  // Sales Chart
  const salesCtx = document.getElementById("salesChart");
  if (salesCtx) {
    new Chart(salesCtx, {
      type: "line",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        datasets: [
          {
            label: "Sales",
            data: [1200000, 1900000, 800000, 2100000, 1600000, 2400000],
            borderColor: "#4299e1",
            backgroundColor: "rgba(66, 153, 225, 0.1)",
            borderWidth: 3,
            fill: true,
            tension: 0.4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          x: {
            grid: {
              color: "rgba(255, 255, 255, 0.1)",
            },
            ticks: {
              color: "rgba(255, 255, 255, 0.7)",
            },
          },
          y: {
            grid: {
              color: "rgba(255, 255, 255, 0.1)",
            },
            ticks: {
              color: "rgba(255, 255, 255, 0.7)",
              callback: (value) => "$" + (value / 1000000).toFixed(1) + "M",
            },
          },
        },
      },
    });
  }

  // Category Chart
  const categoryCtx = document.getElementById("categoryChart");
  if (categoryCtx) {
    new Chart(categoryCtx, {
      type: "doughnut",
      data: {
        labels: ["Sport Series", "Super Series", "Ultimate Series"],
        datasets: [
          {
            data: [45, 35, 20],
            backgroundColor: ["#4299e1", "#48bb78", "#ed8936"],
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
            labels: {
              color: "rgba(255, 255, 255, 0.7)",
              padding: 20,
              usePointStyle: true,
            },
          },
        },
        cutout: "70%",
      },
    });
  }

  // Revenue Chart
  const revenueCtx = document.getElementById("revenueChart");
  if (revenueCtx) {
    new Chart(revenueCtx, {
      type: "bar",
      data: {
        labels: ["Week 1", "Week 2", "Week 3", "Week 4"],
        datasets: [
          {
            label: "Revenue",
            data: [580000, 720000, 650000, 890000],
            backgroundColor: "rgba(66, 153, 225, 0.8)",
            borderColor: "#4299e1",
            borderWidth: 1,
            borderRadius: 8,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          x: {
            grid: {
              color: "rgba(255, 255, 255, 0.1)",
            },
            ticks: {
              color: "rgba(255, 255, 255, 0.7)",
            },
          },
          y: {
            grid: {
              color: "rgba(255, 255, 255, 0.1)",
            },
            ticks: {
              color: "rgba(255, 255, 255, 0.7)",
              callback: (value) => "$" + (value / 1000).toFixed(0) + "K",
            },
          },
        },
      },
    });
  }

  // Customer Chart
  const customerCtx = document.getElementById("customerChart");
  if (customerCtx) {
    new Chart(customerCtx, {
      type: "line",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        datasets: [
          {
            label: "New Customers",
            data: [120, 190, 80, 210, 160, 240],
            borderColor: "#48bb78",
            backgroundColor: "rgba(72, 187, 120, 0.1)",
            borderWidth: 3,
            fill: true,
            tension: 0.4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
        },
        scales: {
          x: {
            grid: {
              color: "rgba(255, 255, 255, 0.1)",
            },
            ticks: {
              color: "rgba(255, 255, 255, 0.7)",
            },
          },
          y: {
            grid: {
              color: "rgba(255, 255, 255, 0.1)",
            },
            ticks: {
              color: "rgba(255, 255, 255, 0.7)",
            },
          },
        },
      },
    });
  }

  // Sales by Category Chart
  const salesByCategoryCtx = document.getElementById("salesByCategoryChart");
  if (salesByCategoryCtx) {
    new Chart(salesByCategoryCtx, {
      type: "polarArea",
      data: {
        labels: ["Sport Series", "Super Series", "Ultimate Series"],
        datasets: [
          {
            data: [1200000, 1800000, 2700000],
            backgroundColor: [
              "rgba(66, 153, 225, 0.8)",
              "rgba(72, 187, 120, 0.8)",
              "rgba(237, 137, 54, 0.8)",
            ],
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
            labels: {
              color: "rgba(255, 255, 255, 0.7)",
              padding: 20,
              usePointStyle: true,
            },
          },
        },
        scales: {
          r: {
            grid: {
              color: "rgba(255, 255, 255, 0.1)",
            },
            ticks: {
              color: "rgba(255, 255, 255, 0.7)",
              backdropColor: "transparent",
            },
          },
        },
      },
    });
  }
}

// Modal Initialization
function initializeModals() {
  const modals = document.querySelectorAll(".modal");
  const modalTriggers = document.querySelectorAll("[data-modal]");
  const modalCloses = document.querySelectorAll(".modal-close");

  // Open modals
  modalTriggers.forEach((trigger) => {
    trigger.addEventListener("click", function () {
      const modalId = this.dataset.modal;
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add("active");
        document.body.style.overflow = "hidden";
      }
    });
  });

  // Add vehicle modal trigger
  const addVehicleBtn = document.getElementById("addVehicleBtn");
  if (addVehicleBtn) {
    addVehicleBtn.addEventListener("click", () => {
      const modal = document.getElementById("vehicleModal");
      if (modal) {
        modal.classList.add("active");
        document.body.style.overflow = "hidden";
      }
    });
  }

  // Close modals
  modalCloses.forEach((close) => {
    close.addEventListener("click", function () {
      const modal = this.closest(".modal");
      if (modal) {
        modal.classList.remove("active");
        document.body.style.overflow = "auto";
      }
    });
  });

  // Close modal on backdrop click
  modals.forEach((modal) => {
    modal.addEventListener("click", function (e) {
      if (e.target === this) {
        this.classList.remove("active");
        document.body.style.overflow = "auto";
      }
    });
  });
}

// Event Listeners
function initializeEventListeners() {
  // Search functionality
  const searchInputs = document.querySelectorAll(".search-box input");
  searchInputs.forEach((input) => {
    input.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase();
      const table =
        this.closest(".table-container").querySelector("table tbody");
      const rows = table.querySelectorAll("tr");

      rows.forEach((row) => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? "" : "none";
      });
    });
  });

  // Filter functionality
  const filterSelects = document.querySelectorAll(".filter-select");
  filterSelects.forEach((select) => {
    select.addEventListener("change", function () {
      // Add filter logic here
      console.log("Filter changed:", this.value);
    });
  });

  // // Action buttons
  // const editButtons = document.querySelectorAll(".action-btn.edit");
  // const deleteButtons = document.querySelectorAll(".action-btn.delete");
  // const viewButtons = document.querySelectorAll(".action-btn.view");

  // editButtons.forEach((btn) => {
  //   btn.addEventListener("click", () => {
  //     console.log("Edit clicked");
  //     // Add edit functionality
  //   });
  // });

  // deleteButtons.forEach((btn) => {
  //   btn.addEventListener("click", () => {
  //     if (confirm("Are you sure you want to delete this item?")) {
  //       console.log("Delete confirmed");
  //       // Add delete functionality
  //     }
  //   });
  // });

  // viewButtons.forEach((btn) => {
  //   btn.addEventListener("click", () => {
  //     console.log("View clicked");
  //     // Add view functionality
  //   });
  // });

  // Chart controls
  const chartButtons = document.querySelectorAll(".chart-btn, .card-btn");
  chartButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      // Remove active class from siblings
      const siblings = this.parentElement.querySelectorAll(
        ".chart-btn, .card-btn"
      );
      siblings.forEach((sibling) => sibling.classList.remove("active"));

      // Add active class to clicked button
      this.classList.add("active");

      // Update chart data based on selection
      console.log("Chart period changed:", this.textContent);
    });
  });

  // Form submissions
  // const forms = document.querySelectorAll("form")
  // forms.forEach((form) => {
  //   form.addEventListener("submit", (e) => {
  //     e.preventDefault()
  //     console.log("Form submitted")
  //     // Add form submission logic
  //   })
  // })

  // Notification handling
  const notificationBtn = document.querySelector(
    '.action-btn[title="Notifications"]'
  );
  if (notificationBtn) {
    notificationBtn.addEventListener("click", () => {
      console.log("Notifications clicked");
      // Add notification dropdown logic
    });
  }
}

// Utility Functions
function formatCurrency(amount) {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
}

function formatDate(date) {
  return new Intl.DateTimeFormat("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  }).format(new Date(date));
}

function showNotification(message, type = "info") {
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.textContent = message;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.classList.add("show");
  }, 100);

  setTimeout(() => {
    notification.classList.remove("show");
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}

// Data Management Functions
function loadVehicles() {
  // Simulate API call
  console.log("Loading vehicles...");
  // Add actual API integration here
}

function loadCustomers() {
  // Simulate API call
  console.log("Loading customers...");
  // Add actual API integration here
}

function loadOrders() {
  // Simulate API call
  console.log("Loading orders...");
  // Add actual API integration here
}

function updateStats() {
  // Simulate stats update
  console.log("Updating statistics...");
  // Add actual stats calculation here
}

// Export functions for external use
window.AdminDashboard = {
  formatCurrency,
  formatDate,
  showNotification,
  loadVehicles,
  loadCustomers,
  loadOrders,
  updateStats,
};
