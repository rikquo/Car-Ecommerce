// Vehicle Management JavaScript
import gsap from "gsap" // Declare the gsap variable

document.addEventListener("DOMContentLoaded", () => {
  initializeVehicleManagement()
  initializeModals()
  initializeEventListeners()
  initializeFormTabs()
  initializeTableFeatures()
})

// Initialize Vehicle Management
function initializeVehicleManagement() {
  console.log("Vehicle Management initialized")
  loadVehicles()
  updateStats()
}

// Initialize Modals
function initializeModals() {
  const modals = document.querySelectorAll(".modal")
  const modalCloses = document.querySelectorAll(".modal-close")

  // Close modals
  modalCloses.forEach((close) => {
    close.addEventListener("click", function () {
      const modal = this.closest(".modal")
      if (modal) {
        closeModal(modal)
      }
    })
  })

  // Close modal on backdrop click
  modals.forEach((modal) => {
    modal.addEventListener("click", function (e) {
      if (e.target === this) {
        closeModal(this)
      }
    })
  })

  // Add Vehicle Button
  const addVehicleBtn = document.getElementById("addVehicleBtn")
  if (addVehicleBtn) {
    addVehicleBtn.addEventListener("click", () => {
      openAddVehicleModal()
    })
  }

  // Cancel Vehicle Button
  const cancelVehicleBtn = document.getElementById("cancelVehicle")
  if (cancelVehicleBtn) {
    cancelVehicleBtn.addEventListener("click", () => {
      const modal = document.getElementById("vehicleModal")
      closeModal(modal)
    })
  }
}

// Initialize Event Listeners
function initializeEventListeners() {
  // Search functionality
  const searchInput = document.getElementById("searchInput")
  if (searchInput) {
    searchInput.addEventListener("input", debounce(handleSearch, 300))
  }

  // Filter functionality
  const filterSelects = document.querySelectorAll(".filter-select")
  filterSelects.forEach((select) => {
    select.addEventListener("change", handleFilter)
  })

  // Advanced search toggle
  const advancedSearchBtn = document.getElementById("advancedSearchBtn")
  const advancedSearchPanel = document.getElementById("advancedSearchPanel")
  const closeAdvancedSearch = document.getElementById("closeAdvancedSearch")

  if (advancedSearchBtn && advancedSearchPanel) {
    advancedSearchBtn.addEventListener("click", () => {
      advancedSearchPanel.classList.toggle("active")
    })
  }

  if (closeAdvancedSearch && advancedSearchPanel) {
    closeAdvancedSearch.addEventListener("click", () => {
      advancedSearchPanel.classList.remove("active")
    })
  }

  // Clear filters
  const clearFiltersBtn = document.getElementById("clearFiltersBtn")
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener("click", clearAllFilters)
  }

  // Select all checkbox
  const selectAllCheckbox = document.getElementById("selectAll")
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener("change", handleSelectAll)
  }

  // Row checkboxes
  document.addEventListener("change", (e) => {
    if (e.target.classList.contains("row-checkbox")) {
      handleRowSelection()
    }
  })

  // Bulk actions
  const bulkDeleteBtn = document.getElementById("bulkDeleteBtn")
  const bulkStatusBtn = document.getElementById("bulkStatusBtn")

  if (bulkDeleteBtn) {
    bulkDeleteBtn.addEventListener("click", handleBulkDelete)
  }

  if (bulkStatusBtn) {
    bulkStatusBtn.addEventListener("click", handleBulkStatusChange)
  }

  // Form submission
  const vehicleForm = document.getElementById("vehicleForm")
  if (vehicleForm) {
    vehicleForm.addEventListener("submit", handleFormSubmission)
  }

  // File upload handling
  const primaryImageInput = document.getElementById("primaryImage")
  const additionalImagesInput = document.getElementById("additionalImages")

  if (primaryImageInput) {
    primaryImageInput.addEventListener("change", handlePrimaryImageUpload)
  }

  if (additionalImagesInput) {
    additionalImagesInput.addEventListener("change", handleAdditionalImagesUpload)
  }
}

// Initialize Form Tabs
function initializeFormTabs() {
  const tabBtns = document.querySelectorAll(".tab-btn")
  const tabContents = document.querySelectorAll(".tab-content")

  tabBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      const tabId = btn.dataset.tab

      // Remove active class from all tabs and contents
      tabBtns.forEach((b) => b.classList.remove("active"))
      tabContents.forEach((c) => c.classList.remove("active"))

      // Add active class to clicked tab and corresponding content
      btn.classList.add("active")
      const targetContent = document.getElementById(`${tabId}-tab`)
      if (targetContent) {
        targetContent.classList.add("active")
      }
    })
  })
}

// Initialize Table Features
function initializeTableFeatures() {
  // Sortable columns
  const sortableHeaders = document.querySelectorAll(".sortable")
  sortableHeaders.forEach((header) => {
    header.addEventListener("click", () => {
      const sortBy = header.dataset.sort
      handleSort(sortBy)
    })
  })

  // Pagination
  const paginationBtns = document.querySelectorAll(".pagination-btn")
  paginationBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      if (!btn.disabled && !btn.classList.contains("active")) {
        const page = btn.textContent
        if (!isNaN(page)) {
          handlePagination(Number.parseInt(page))
        }
      }
    })
  })
}

// Vehicle CRUD Operations
function openAddVehicleModal() {
  const modal = document.getElementById("vehicleModal")
  const modalTitle = document.getElementById("modalTitle")
  const form = document.getElementById("vehicleForm")

  modalTitle.textContent = "Add New Vehicle"
  form.reset()
  form.dataset.mode = "add"
  form.dataset.vehicleId = ""

  openModal(modal)
}

function editVehicle(vehicleId) {
  const modal = document.getElementById("vehicleModal")
  const modalTitle = document.getElementById("modalTitle")
  const form = document.getElementById("vehicleForm")

  modalTitle.textContent = "Edit Vehicle"
  form.dataset.mode = "edit"
  form.dataset.vehicleId = vehicleId

  // Load vehicle data and populate form
  loadVehicleData(vehicleId)
  openModal(modal)
}

function viewVehicle(vehicleId) {
  const modal = document.getElementById("vehicleDetailsModal")
  const modalTitle = document.getElementById("detailsModalTitle")
  const content = document.getElementById("vehicleDetailsContent")

  modalTitle.textContent = "Vehicle Details"
  loadVehicleDetails(vehicleId, content)
  openModal(modal)
}

function deleteVehicle(vehicleId) {
  const modal = document.getElementById("deleteModal")
  const confirmBtn = document.getElementById("confirmDelete")

  confirmBtn.onclick = () => {
    performDelete(vehicleId)
    closeModal(modal)
  }

  openModal(modal)
}

function showMoreOptions(vehicleId) {
  // Create context menu or dropdown for additional options
  console.log("Show more options for vehicle:", vehicleId)
  // Implementation for additional actions like duplicate, archive, etc.
}

// Modal Helper Functions
function openModal(modal) {
  modal.classList.add("active")
  document.body.style.overflow = "hidden"

  // Animate modal in
  gsap.fromTo(
    modal.querySelector(".modal-content"),
    { opacity: 0, scale: 0.9, y: 20 },
    { opacity: 1, scale: 1, y: 0, duration: 0.3, ease: "power2.out" },
  )
}

function closeModal(modal) {
  gsap.to(modal.querySelector(".modal-content"), {
    opacity: 0,
    scale: 0.9,
    y: 20,
    duration: 0.2,
    ease: "power2.in",
    onComplete: () => {
      modal.classList.remove("active")
      document.body.style.overflow = "auto"
    },
  })
}

// Search and Filter Functions
function handleSearch(event) {
  const searchTerm = event.target.value.toLowerCase()
  console.log("Searching for:", searchTerm)

  // Filter table rows based on search term
  const rows = document.querySelectorAll(".vehicle-row")
  let visibleCount = 0

  rows.forEach((row) => {
    const text = row.textContent.toLowerCase()
    const isVisible = text.includes(searchTerm)

    row.style.display = isVisible ? "" : "none"
    if (isVisible) visibleCount++
  })

  updateResultCount(visibleCount)
}

function handleFilter(event) {
  const filterType = event.target.id
  const filterValue = event.target.value

  console.log(`Filter ${filterType}:`, filterValue)
  applyFilters()
}

function applyFilters() {
  const seriesFilter = document.getElementById("seriesFilter").value
  const statusFilter = document.getElementById("statusFilter").value
  const yearFilter = document.getElementById("yearFilter").value

  const rows = document.querySelectorAll(".vehicle-row")
  let visibleCount = 0

  rows.forEach((row) => {
    let isVisible = true

    // Apply series filter
    if (seriesFilter) {
      const seriesBadge = row.querySelector(".series-badge")
      const series = seriesBadge ? seriesBadge.textContent.toLowerCase() : ""
      isVisible = isVisible && series.includes(seriesFilter)
    }

    // Apply status filter
    if (statusFilter) {
      const statusBadge = row.querySelector(".status-badge")
      const status = statusBadge ? statusBadge.classList[1] : ""
      isVisible = isVisible && status === statusFilter
    }

    // Apply year filter
    if (yearFilter) {
      const yearCell = row.cells[4] // Assuming year is in 5th column
      const year = yearCell ? yearCell.textContent : ""
      isVisible = isVisible && year === yearFilter
    }

    row.style.display = isVisible ? "" : "none"
    if (isVisible) visibleCount++
  })

  updateResultCount(visibleCount)
}

function clearAllFilters() {
  // Reset all filter selects
  document.getElementById("seriesFilter").value = ""
  document.getElementById("statusFilter").value = ""
  document.getElementById("searchInput").value = ""

  // Clear advanced search
  document.getElementById("minPrice").value = ""
  document.getElementById("maxPrice").value = ""
  document.getElementById("engineFilter").value = ""
  document.getElementById("minHP").value = ""
  document.getElementById("maxHP").value = ""

  // Show all rows
  const rows = document.querySelectorAll(".vehicle-row")
  rows.forEach((row) => {
    row.style.display = ""
  })

  updateResultCount(rows.length)
}

// Selection Functions
function handleSelectAll(event) {
  const isChecked = event.target.checked
  const rowCheckboxes = document.querySelectorAll(".row-checkbox")

  rowCheckboxes.forEach((checkbox) => {
    checkbox.checked = isChecked
  })

  handleRowSelection()
}

function handleRowSelection() {
  const selectedCheckboxes = document.querySelectorAll(".row-checkbox:checked")
  const bulkDeleteBtn = document.getElementById("bulkDeleteBtn")
  const bulkStatusBtn = document.getElementById("bulkStatusBtn")

  const hasSelection = selectedCheckboxes.length > 0

  if (bulkDeleteBtn) bulkDeleteBtn.disabled = !hasSelection
  if (bulkStatusBtn) bulkStatusBtn.disabled = !hasSelection

  // Update select all checkbox state
  const selectAllCheckbox = document.getElementById("selectAll")
  const totalCheckboxes = document.querySelectorAll(".row-checkbox")

  if (selectAllCheckbox) {
    selectAllCheckbox.indeterminate =
      selectedCheckboxes.length > 0 && selectedCheckboxes.length < totalCheckboxes.length
    selectAllCheckbox.checked = selectedCheckboxes.length === totalCheckboxes.length
  }
}

// Bulk Operations
function handleBulkDelete() {
  const selectedIds = getSelectedVehicleIds()
  if (selectedIds.length === 0) return

  if (confirm(`Are you sure you want to delete ${selectedIds.length} vehicle(s)?`)) {
    console.log("Bulk deleting vehicles:", selectedIds)
    // Implement bulk delete logic
    showNotification(`${selectedIds.length} vehicle(s) deleted successfully`, "success")
  }
}

function handleBulkStatusChange() {
  const selectedIds = getSelectedVehicleIds()
  if (selectedIds.length === 0) return

  const newStatus = prompt("Enter new status (available, reserved, sold, maintenance):")
  if (newStatus && ["available", "reserved", "sold", "maintenance"].includes(newStatus)) {
    console.log("Bulk status change:", selectedIds, "to", newStatus)
    // Implement bulk status change logic
    showNotification(`Status updated for ${selectedIds.length} vehicle(s)`, "success")
  }
}

function getSelectedVehicleIds() {
  const selectedCheckboxes = document.querySelectorAll(".row-checkbox:checked")
  return Array.from(selectedCheckboxes).map((checkbox) => checkbox.dataset.id)
}

// Form Handling
function handleFormSubmission(event) {
  event.preventDefault()

  const form = event.target
  const formData = new FormData(form)
  const mode = form.dataset.mode
  const vehicleId = form.dataset.vehicleId

  console.log(`${mode === "add" ? "Adding" : "Updating"} vehicle:`, Object.fromEntries(formData))

  // Simulate API call
  setTimeout(() => {
    const modal = document.getElementById("vehicleModal")
    closeModal(modal)
    showNotification(`Vehicle ${mode === "add" ? "added" : "updated"} successfully!`, "success")
    loadVehicles() // Refresh the table
  }, 1000)
}

// File Upload Handling
function handlePrimaryImageUpload(event) {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      // Show preview
      console.log("Primary image uploaded:", file.name)
    }
    reader.readAsDataURL(file)
  }
}

function handleAdditionalImagesUpload(event) {
  const files = Array.from(event.target.files)
  const previewGrid = document.getElementById("imagePreviewGrid")

  previewGrid.innerHTML = ""

  files.forEach((file, index) => {
    const reader = new FileReader()
    reader.onload = (e) => {
      const previewDiv = document.createElement("div")
      previewDiv.className = "image-preview"
      previewDiv.innerHTML = `
        <img src="${e.target.result}" alt="Preview ${index + 1}">
        <button type="button" class="remove-image" onclick="removeImagePreview(this)">
          <i class="fas fa-times"></i>
        </button>
      `
      previewGrid.appendChild(previewDiv)
    }
    reader.readAsDataURL(file)
  })
}

function removeImagePreview(button) {
  button.closest(".image-preview").remove()
}

// Table Operations
function handleSort(sortBy) {
  console.log("Sorting by:", sortBy)
  // Implement sorting logic
}

function handlePagination(page) {
  console.log("Navigate to page:", page)
  // Implement pagination logic
}

// Data Loading Functions
function loadVehicles() {
  console.log("Loading vehicles...")
  // Simulate loading state
  const tableBody = document.getElementById("vehiclesTableBody")
  if (tableBody) {
    tableBody.classList.add("loading")

    setTimeout(() => {
      tableBody.classList.remove("loading")
      updateResultCount(127) // Update with actual count
    }, 1000)
  }
}

function loadVehicleData(vehicleId) {
  console.log("Loading vehicle data for ID:", vehicleId)
  // Simulate API call to load vehicle data
  // Populate form fields with loaded data
}

function loadVehicleDetails(vehicleId, container) {
  console.log("Loading vehicle details for ID:", vehicleId)

  // Sample vehicle details
  const sampleDetails = `
    <div class="details-section">
      <h4>Basic Information</h4>
      <div class="details-grid">
        <div class="detail-item">
          <span class="detail-label">Model:</span>
          <span class="detail-value">McLaren 720S</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Series:</span>
          <span class="detail-value">Super Series</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Year:</span>
          <span class="detail-value">2024</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">VIN:</span>
          <span class="detail-value">MC720S2024001</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Color:</span>
          <span class="detail-value">Volcano Orange</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Status:</span>
          <span class="detail-value">Available</span>
        </div>
      </div>
    </div>
    
    <div class="details-section">
      <h4>Specifications</h4>
      <div class="details-grid">
        <div class="detail-item">
          <span class="detail-label">Engine:</span>
          <span class="detail-value">4.0L Twin-Turbo V8</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Horsepower:</span>
          <span class="detail-value">710 HP</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Torque:</span>
          <span class="detail-value">568 lb-ft</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">0-60 mph:</span>
          <span class="detail-value">2.8s</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Top Speed:</span>
          <span class="detail-value">212 mph</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Weight:</span>
          <span class="detail-value">3,186 lbs</span>
        </div>
      </div>
    </div>
  `

  container.innerHTML = sampleDetails
}

function performDelete(vehicleId) {
  console.log("Deleting vehicle:", vehicleId)
  // Simulate API call
  setTimeout(() => {
    showNotification("Vehicle deleted successfully!", "success")
    loadVehicles() // Refresh the table
  }, 500)
}

// Utility Functions
function updateStats() {
  console.log("Updating vehicle statistics...")
  // Update the stats cards with real data
}

function updateResultCount(count) {
  const resultCountElement = document.getElementById("resultCount")
  if (resultCountElement) {
    resultCountElement.textContent = count
  }
}

function showNotification(message, type = "info") {
  const notification = document.createElement("div")
  notification.className = `notification notification-${type}`
  notification.innerHTML = `
    <i class="fas fa-${type === "success" ? "check-circle" : type === "error" ? "exclamation-circle" : "info-circle"}"></i>
    <span>${message}</span>
  `

  document.body.appendChild(notification)

  // Animate in
  gsap.fromTo(notification, { opacity: 0, y: -50, x: "50%" }, { opacity: 1, y: 20, duration: 0.3, ease: "power2.out" })

  // Auto remove after 3 seconds
  setTimeout(() => {
    gsap.to(notification, {
      opacity: 0,
      y: -50,
      duration: 0.3,
      ease: "power2.in",
      onComplete: () => {
        if (notification.parentNode) {
          notification.parentNode.removeChild(notification)
        }
      },
    })
  }, 3000)
}

function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Export functions for global access
window.VehicleManagement = {
  editVehicle,
  viewVehicle,
  deleteVehicle,
  showMoreOptions,
  removeImagePreview,
}
