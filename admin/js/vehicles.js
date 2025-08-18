// Vehicle Management JavaScript - No AJAX Version

document.addEventListener("DOMContentLoaded", function () {
  initializeModals();
  initializeEventListeners();
});

// Initialize Modals
function initializeModals() {
  const modal = document.getElementById("vehicleModal");
  const addVehicleBtn = document.getElementById("addVehicleBtn");
  const closeButtons = document.querySelectorAll(".modal-close");

  // Add Vehicle Button
  if (addVehicleBtn) {
    addVehicleBtn.addEventListener("click", function () {
      openAddModal();
    });
  }

  // Close modal functionality
  if (closeButtons.length > 0) {
    closeButtons.forEach((button) => {
      button.addEventListener("click", function (e) {
        e.preventDefault();
        closeModal();
      });
    });
  }

  // Close modal when clicking outside
  if (modal) {
    window.addEventListener("click", function (event) {
      if (event.target === modal) {
        closeModal();
      }
    });
  }

  // ESC key to close modal
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && modal && modal.classList.contains("open")) {
      closeModal();
    }
  });
}

// Initialize Event Listeners
function initializeEventListeners() {
  // Search functionality
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
    searchInput.addEventListener("input", debounce(handleSearch, 300));
  }

  // Filter functionality
  const seriesFilter = document.getElementById("seriesFilter");
  if (seriesFilter) {
    seriesFilter.addEventListener("change", handleFilter);
  }

  // Clear filters
  const clearFiltersBtn = document.getElementById("clearFiltersBtn");
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener("click", clearFilters);
  }

  // Store original row count for filtering
  const tableRows = document.querySelectorAll(".vehicle-row");
  window.originalRowCount = tableRows.length;
}

// Modal Functions
function openAddModal() {
  const modal = document.getElementById("vehicleModal");
  const modalTitle = document.getElementById("modalTitle");
  const submitButtonText = document.getElementById("submitButtonText");
  const vehicleForm = document.getElementById("vehicleForm");

  if (modal && modalTitle && submitButtonText && vehicleForm) {
    // Reset form
    vehicleForm.reset();
    document.getElementById("vehicleId").value = "";
    document.getElementById("formAction").value = "add";

    // Set form action for add
    vehicleForm.action = "actions/add_vehicles.php";

    // Hide current image preview
    const currentImage = document.getElementById("currentImage");
    if (currentImage) {
      currentImage.style.display = "none";
    }

    // Make image field required for add
    const vehicleImage = document.getElementById("vehicleImage");
    if (vehicleImage) {
      vehicleImage.setAttribute("required", "required");
    }

    modalTitle.textContent = "Add New Vehicle";
    submitButtonText.textContent = "Save Vehicle";
    modal.style.display = "block";
    modal.classList.add("open");
  }
}

function openEditModal(vehicleData) {
  const modal = document.getElementById("vehicleModal");
  const modalTitle = document.getElementById("modalTitle");
  const submitButtonText = document.getElementById("submitButtonText");
  const vehicleForm = document.getElementById("vehicleForm");

  if (modal && modalTitle && submitButtonText && vehicleForm) {
    // Set form action for edit
    vehicleForm.action = "actions/update_vehicle.php";

    // Populate form fields
    document.getElementById("vehicleId").value = vehicleData.car_id;
    document.getElementById("formAction").value = "edit";
    document.getElementById("vehicleModel").value = vehicleData.name;
    document.getElementById("vehicleSeries").value = vehicleData.series_id;
    document.getElementById("vehicleEngine").value = vehicleData.engine;
    document.getElementById("vehiclePower").value = vehicleData.power_hp;
    document.getElementById("vehicleDoors").value = vehicleData.doors;
    document.getElementById("vehicleMph").value = vehicleData.acceleration_0_60;
    document.getElementById("vehiclePrice").value = vehicleData.price;
    document.getElementById("vehicleDescription").value =
      vehicleData.description;
    document.getElementById("vehicleStock").value = vehicleData.stock_quantity;

    // Show current image if exists
    if (vehicleData.image_url) {
      const currentImage = document.getElementById("currentImage");
      const currentImagePreview = document.getElementById(
        "currentImagePreview"
      );
      const vehicleImage = document.getElementById("vehicleImage");

      if (currentImage && currentImagePreview) {
        currentImage.style.display = "block";
        currentImagePreview.src = vehicleData.image_url;
      }

      // Make image field optional for edit
      if (vehicleImage) {
        vehicleImage.removeAttribute("required");
      }
    }

    modalTitle.textContent = "Edit Vehicle";
    submitButtonText.textContent = "Update Vehicle";
    modal.style.display = "block";
    modal.classList.add("open");
  }
}

function closeModal() {
  const modal = document.getElementById("vehicleModal");
  if (modal) {
    modal.style.display = "none";
    modal.classList.remove("open");
  }
}

// Search and Filter Functions (Client-side only)
function handleSearch(event) {
  const query = event.target.value.toLowerCase().trim();
  const tableRows = document.querySelectorAll(".vehicle-row");
  let visibleCount = 0;

  tableRows.forEach((row) => {
    const vehicleName = row.querySelector(".vehicle-name");
    if (vehicleName) {
      const name = vehicleName.textContent.toLowerCase();
      if (query === "" || name.includes(query)) {
        row.style.display = "";
        visibleCount++;
      } else {
        row.style.display = "none";
      }
    }
  });

  updateResultCount(visibleCount);
}

function handleFilter() {
  const seriesFilter = document.getElementById("seriesFilter");
  const selectedSeriesId = seriesFilter ? seriesFilter.value : "";
  const tableRows = document.querySelectorAll(".vehicle-row");
  let visibleCount = 0;

  tableRows.forEach((row) => {
    const seriesId = row.getAttribute("data-series-id");
    if (selectedSeriesId === "" || seriesId === selectedSeriesId) {
      row.style.display = "";
      visibleCount++;
    } else {
      row.style.display = "none";
    }
  });

  updateResultCount(visibleCount);
}

function clearFilters() {
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
    searchInput.value = "";
  }

  const seriesFilter = document.getElementById("seriesFilter");
  if (seriesFilter) {
    seriesFilter.value = "";
  }

  const tableRows = document.querySelectorAll(".vehicle-row");
  tableRows.forEach((row) => {
    row.style.display = "";
  });

  updateResultCount(window.originalRowCount || tableRows.length);
}

function updateResultCount(count) {
  const resultCount = document.getElementById("resultCount");
  if (resultCount) {
    resultCount.textContent = count;
  }
}

// Utility Functions
function debounce(func, delay) {
  let timeoutId;
  return function (...args) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      func.apply(this, args);
    }, delay);
  };
}

// Global functions for inline onclick handlers
window.openEditModal = openEditModal;
