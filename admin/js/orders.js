// Orders Management JavaScript

document.addEventListener("DOMContentLoaded", () => {
  initializeModals();
  initializeEventListeners();
  initializeAlerts();
});

// Initialize Modals
function initializeModals() {
  const modals = document.querySelectorAll(".modal");
  const closeButtons = document.querySelectorAll(".modal-close");

  // Close modal functionality
  closeButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      closeModal(this.closest(".modal"));
    });
  });

  // Close modal when clicking outside
  modals.forEach((modal) => {
    modal.addEventListener("click", function (e) {
      if (e.target === this) {
        closeModal(this);
      }
    });
  });

  // ESC key to close modal
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      const openModal = document.querySelector(".modal.open");
      if (openModal) {
        closeModal(openModal);
      }
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
  const statusFilter = document.getElementById("statusFilter");
  if (statusFilter) {
    statusFilter.addEventListener("change", handleStatusFilter);
  }

  const dateFilter = document.getElementById("dateFilter");
  if (dateFilter) {
    dateFilter.addEventListener("change", handleDateFilter);
  }

  // Clear filters
  const clearFiltersBtn = document.getElementById("clearFiltersBtn");
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener("click", clearFilters);
  }

  // Export orders
  const exportOrdersBtn = document.getElementById("exportOrdersBtn");
  if (exportOrdersBtn) {
    exportOrdersBtn.addEventListener("click", exportOrders);
  }

  // Store original row count for filtering
  const tableRows = document.querySelectorAll(".order-row");
  window.originalRowCount = tableRows.length;
}

// Initialize Alerts
function initializeAlerts() {
  const alertMessage = document.getElementById("alertMessage");
  if (alertMessage) {
    setTimeout(() => {
      alertMessage.style.opacity = "0";
      setTimeout(() => {
        alertMessage.remove();
      }, 300);
    }, 5000);
  }
}

// Modal Functions
function openModal(modal) {
  if (modal) {
    modal.style.display = "block";
    modal.classList.add("open");
    document.body.style.overflow = "hidden";
  }
}

function closeModal(modal) {
  if (modal) {
    modal.style.display = "none";
    modal.classList.remove("open");
    document.body.style.overflow = "auto";
  }
}

// Order Details Modal
function viewOrderDetails(orderId) {
  const modal = document.getElementById("orderDetailsModal");
  const modalTitle = document.getElementById("orderDetailsTitle");
  const modalBody = document.getElementById("orderDetailsBody");

  if (modal && modalTitle && modalBody) {
    modalTitle.textContent = `Order #ORD-${String(orderId).padStart(
      3,
      "0"
    )} Details`;
    modalBody.innerHTML = `
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading order details...</p>
            </div>
        `;

    openModal(modal);

    // Simulate loading order details (replace with actual data fetching)
    setTimeout(() => {
      modalBody.innerHTML = `
                <div class="order-details">
                    <div class="detail-section">
                        <h4>Order Information</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Order ID:</label>
                                <span>#ORD-${String(orderId).padStart(
                                  3,
                                  "0"
                                )}</span>
                            </div>
                            <div class="detail-item">
                                <label>Order Date:</label>
                                <span>${new Date().toLocaleDateString()}</span>
                            </div>
                            <div class="detail-item">
                                <label>Status:</label>
                                <span class="status-badge pending">Pending</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4>Customer Information</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Name:</label>
                                <span>Loading...</span>
                            </div>
                            <div class="detail-item">
                                <label>Email:</label>
                                <span>Loading...</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4>Vehicle Information</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Model:</label>
                                <span>Loading...</span>
                            </div>
                            <div class="detail-item">
                                <label>Price:</label>
                                <span>Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
    }, 1000);
  }
}

// Status Update Modal
function openStatusModal(orderData) {
  const modal = document.getElementById("statusModal");
  const orderIdInput = document.getElementById("statusOrderId");
  const currentStatusInput = document.getElementById("currentStatus");
  const newStatusSelect = document.getElementById("newStatus");
  const notesTextarea = document.getElementById("statusNotes");

  if (modal && orderIdInput && currentStatusInput && newStatusSelect) {
    orderIdInput.value = orderData.order_id;
    currentStatusInput.value = orderData.status; // Already capitalized from database
    newStatusSelect.value = "";
    if (notesTextarea) {
      notesTextarea.value = "";
    }

    // Update the select options to match your ENUM
    newStatusSelect.innerHTML = `
      <option value="">Select Status</option>
      <option value="Pending">Pending</option>
      <option value="Completed">Completed</option>
      <option value="Cancelled">Cancelled</option>
    `;

    openModal(modal);
  }
}

// Search and Filter Functions
function handleSearch(event) {
  const query = event.target.value.toLowerCase().trim();
  const tableRows = document.querySelectorAll(".order-row");
  let visibleCount = 0;

  tableRows.forEach((row) => {
    const orderNumber = row.querySelector(".order-number");
    const customerName = row.querySelector(".customer-name");
    const vehicleName = row.querySelector(".vehicle-name");

    let shouldShow = false;

    if (query === "") {
      shouldShow = true;
    } else {
      if (
        orderNumber &&
        orderNumber.textContent.toLowerCase().includes(query)
      ) {
        shouldShow = true;
      }
      if (
        customerName &&
        customerName.textContent.toLowerCase().includes(query)
      ) {
        shouldShow = true;
      }
      if (
        vehicleName &&
        vehicleName.textContent.toLowerCase().includes(query)
      ) {
        shouldShow = true;
      }
    }

    if (shouldShow) {
      row.style.display = "";
      visibleCount++;
    } else {
      row.style.display = "none";
    }
  });

  updateResultCount(visibleCount);
}

function handleStatusFilter() {
  const statusFilter = document.getElementById("statusFilter");
  const selectedStatus = statusFilter ? statusFilter.value : ""; // Don't convert to lowercase
  const tableRows = document.querySelectorAll(".order-row");
  let visibleCount = 0;

  tableRows.forEach((row) => {
    const rowStatus = row.getAttribute("data-status");
    if (selectedStatus === "" || rowStatus === selectedStatus) {
      row.style.display = "";
      visibleCount++;
    } else {
      row.style.display = "none";
    }
  });

  updateResultCount(visibleCount);
}

function handleDateFilter() {
  const dateFilter = document.getElementById("dateFilter");
  const selectedPeriod = dateFilter ? dateFilter.value : "";

  // This would typically filter by date ranges
  // For now, we'll just show all orders since we don't have date filtering logic
  console.log("Date filter changed:", selectedPeriod);

  // You can implement date filtering logic here
  // For example, filter orders by today, this week, this month, etc.
}

function clearFilters() {
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
    searchInput.value = "";
  }

  const statusFilter = document.getElementById("statusFilter");
  if (statusFilter) {
    statusFilter.value = "";
  }

  const dateFilter = document.getElementById("dateFilter");
  if (dateFilter) {
    dateFilter.value = "";
  }

  const tableRows = document.querySelectorAll(".order-row");
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

// Export Orders
function exportOrders() {
  // Simple CSV export functionality
  const table = document.querySelector(".orders-table");
  if (!table) return;

  const csv = [];
  const rows = table.querySelectorAll("tr");

  for (let i = 0; i < rows.length; i++) {
    const row = [];
    const cols = rows[i].querySelectorAll("td, th");

    for (let j = 0; j < cols.length - 1; j++) {
      // Exclude actions column
      let cellText = cols[j].textContent.trim();
      cellText = cellText.replace(/"/g, '""'); // Escape quotes
      row.push('"' + cellText + '"');
    }

    csv.push(row.join(","));
  }

  const csvContent = csv.join("\n");
  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");

  if (link.download !== undefined) {
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute(
      "download",
      `orders_${new Date().toISOString().split("T")[0]}.csv`
    );
    link.style.visibility = "hidden";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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

function showNotification(message, type = "info") {
  const notification = document.createElement("div");
  notification.className = `alert alert-${type}`;
  notification.textContent = message;
  notification.style.position = "fixed";
  notification.style.top = "20px";
  notification.style.right = "20px";
  notification.style.zIndex = "9999";
  notification.style.opacity = "0";
  notification.style.transition = "opacity 0.3s ease";

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.opacity = "1";
  }, 100);

  setTimeout(() => {
    notification.style.opacity = "0";
    setTimeout(() => {
      if (notification.parentNode) {
        document.body.removeChild(notification);
      }
    }, 300);
  }, 5000);
}

// Global functions for inline onclick handlers
window.viewOrderDetails = viewOrderDetails;
window.openStatusModal = openStatusModal;
