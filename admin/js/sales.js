// Sales Management JavaScript

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
  const periodFilter = document.getElementById("periodFilter");
  if (periodFilter) {
    periodFilter.addEventListener("change", handlePeriodFilter);
  }

  const seriesFilter = document.getElementById("seriesFilter");
  if (seriesFilter) {
    seriesFilter.addEventListener("change", handleSeriesFilter);
  }

  const amountFilter = document.getElementById("amountFilter");
  if (amountFilter) {
    amountFilter.addEventListener("change", handleAmountFilter);
  }

  // Clear filters
  const clearFiltersBtn = document.getElementById("clearFiltersBtn");
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener("click", clearFilters);
  }

  // Export sales
  const exportSalesBtn = document.getElementById("exportSalesBtn");
  if (exportSalesBtn) {
    exportSalesBtn.addEventListener("click", exportSales);
  }

  // Generate report
  const generateReportBtn = document.getElementById("generateReportBtn");
  if (generateReportBtn) {
    generateReportBtn.addEventListener("click", openReportModal);
  }

  // Bulk export
  const bulkExportBtn = document.getElementById("bulkExportBtn");
  if (bulkExportBtn) {
    bulkExportBtn.addEventListener("click", bulkExportSelected);
  }

  // Select all checkbox
  const selectAllCheckbox = document.getElementById("selectAll");
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener("change", handleSelectAll);
  }

  // Individual checkboxes
  const saleCheckboxes = document.querySelectorAll(".sale-checkbox");
  saleCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", handleIndividualSelect);
  });

  // Report type change
  const reportType = document.getElementById("reportType");
  if (reportType) {
    reportType.addEventListener("change", handleReportTypeChange);
  }

  // Store original row count for filtering
  const tableRows = document.querySelectorAll(".sale-row");
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

// Sale Details Modal
function viewSaleDetails(saleId) {
  const modal = document.getElementById("saleDetailsModal");
  const modalTitle = document.getElementById("saleDetailsTitle");
  const modalBody = document.getElementById("saleDetailsBody");

  if (modal && modalTitle && modalBody) {
    modalTitle.textContent = `Sale #SALE-${String(saleId).padStart(
      3,
      "0"
    )} Details`;
    modalBody.innerHTML = `
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading sale details...</p>
            </div>
        `;

    openModal(modal);

    // Simulate loading sale details
    setTimeout(() => {
      modalBody.innerHTML = `
                <div class="sale-details">
                    <div class="detail-section">
                        <h4>Sale Information</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Sale ID:</label>
                                <span>#SALE-${String(saleId).padStart(
                                  3,
                                  "0"
                                )}</span>
                            </div>
                            <div class="detail-item">
                                <label>Sale Date:</label>
                                <span>${new Date().toLocaleDateString()}</span>
                            </div>
                            <div class="detail-item">
                                <label>Status:</label>
                                <span class="status-badge completed">Completed</span>
                            </div>
                            <div class="detail-item">
                                <label>Commission:</label>
                                <span class="commission-amount">$15,000</span>
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
                            <div class="detail-item">
                                <label>Phone:</label>
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
                                <label>Series:</label>
                                <span>Loading...</span>
                            </div>
                            <div class="detail-item">
                                <label>Sale Price:</label>
                                <span>Loading...</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4>Payment Information</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>Payment Method:</label>
                                <span>Bank Transfer</span>
                            </div>
                            <div class="detail-item">
                                <label>Transaction ID:</label>
                                <span>TXN-${Math.random()
                                  .toString(36)
                                  .substr(2, 9)
                                  .toUpperCase()}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
    }, 1000);
  }
}

// Report Modal
function openReportModal() {
  const modal = document.getElementById("reportModal");
  if (modal) {
    openModal(modal);
  }
}

function handleReportTypeChange() {
  const reportType = document.getElementById("reportType");
  const dateRangeGroup = document.getElementById("dateRangeGroup");

  if (reportType && dateRangeGroup) {
    if (reportType.value === "custom") {
      dateRangeGroup.style.display = "block";
    } else {
      dateRangeGroup.style.display = "none";
    }
  }
}

// Invoice and Receipt Functions
function generateInvoice(saleId) {
  showNotification(
    `Generating invoice for Sale #SALE-${String(saleId).padStart(3, "0")}...`,
    "info"
  );

  // Simulate invoice generation
  setTimeout(() => {
    showNotification("Invoice generated successfully!", "success");
  }, 2000);
}

function viewReceipt(saleId) {
  showNotification(
    `Opening receipt for Sale #SALE-${String(saleId).padStart(3, "0")}...`,
    "info"
  );

  // Simulate receipt viewing
  setTimeout(() => {
    showNotification("Receipt opened successfully!", "success");
  }, 1000);
}

// Search and Filter Functions
function handleSearch(event) {
  const query = event.target.value.toLowerCase().trim();
  const tableRows = document.querySelectorAll(".sale-row");
  let visibleCount = 0;

  tableRows.forEach((row) => {
    const saleNumber = row.querySelector(".sale-number");
    const customerName = row.querySelector(".customer-name");
    const vehicleName = row.querySelector(".vehicle-name");

    let shouldShow = false;

    if (query === "") {
      shouldShow = true;
    } else {
      if (saleNumber && saleNumber.textContent.toLowerCase().includes(query)) {
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

function handlePeriodFilter() {
  // This would typically filter by date periods
  console.log("Period filter changed");
  // Implement date filtering logic here
}

function handleSeriesFilter() {
  const seriesFilter = document.getElementById("seriesFilter");
  const selectedSeriesId = seriesFilter ? seriesFilter.value : "";
  const tableRows = document.querySelectorAll(".sale-row");
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

function handleAmountFilter() {
  const amountFilter = document.getElementById("amountFilter");
  const selectedRange = amountFilter ? amountFilter.value : "";
  const tableRows = document.querySelectorAll(".sale-row");
  let visibleCount = 0;

  tableRows.forEach((row) => {
    const amount = Number.parseFloat(row.getAttribute("data-amount"));
    let shouldShow = false;

    if (selectedRange === "") {
      shouldShow = true;
    } else if (selectedRange === "0-200000") {
      shouldShow = amount < 200000;
    } else if (selectedRange === "200000-500000") {
      shouldShow = amount >= 200000 && amount < 500000;
    } else if (selectedRange === "500000-1000000") {
      shouldShow = amount >= 500000 && amount < 1000000;
    } else if (selectedRange === "1000000+") {
      shouldShow = amount >= 1000000;
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

function clearFilters() {
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
    searchInput.value = "";
  }

  const periodFilter = document.getElementById("periodFilter");
  if (periodFilter) {
    periodFilter.value = "";
  }

  const seriesFilter = document.getElementById("seriesFilter");
  if (seriesFilter) {
    seriesFilter.value = "";
  }

  const amountFilter = document.getElementById("amountFilter");
  if (amountFilter) {
    amountFilter.value = "";
  }

  const tableRows = document.querySelectorAll(".sale-row");
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

// Checkbox Functions
function handleSelectAll() {
  const selectAllCheckbox = document.getElementById("selectAll");
  const saleCheckboxes = document.querySelectorAll(".sale-checkbox");

  if (selectAllCheckbox) {
    saleCheckboxes.forEach((checkbox) => {
      checkbox.checked = selectAllCheckbox.checked;
    });
  }
}

function handleIndividualSelect() {
  const selectAllCheckbox = document.getElementById("selectAll");
  const saleCheckboxes = document.querySelectorAll(".sale-checkbox");
  const checkedBoxes = document.querySelectorAll(".sale-checkbox:checked");

  if (selectAllCheckbox) {
    selectAllCheckbox.checked = checkedBoxes.length === saleCheckboxes.length;
  }
}

// Export Functions
function exportSales() {
  const table = document.querySelector(".sales-table");
  if (!table) return;

  const csv = [];
  const rows = table.querySelectorAll("tr");

  for (let i = 0; i < rows.length; i++) {
    const row = [];
    const cols = rows[i].querySelectorAll("td, th");

    for (let j = 1; j < cols.length - 1; j++) {
      // Skip checkbox column (0) and actions column (last)
      let cellText = cols[j].textContent.trim();
      cellText = cellText.replace(/"/g, '""');
      row.push('"' + cellText + '"');
    }

    if (row.length > 0) {
      csv.push(row.join(","));
    }
  }

  const csvContent = csv.join("\n");
  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");

  if (link.download !== undefined) {
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute(
      "download",
      `sales_report_${new Date().toISOString().split("T")[0]}.csv`
    );
    link.style.visibility = "hidden";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
}

function bulkExportSelected() {
  const checkedBoxes = document.querySelectorAll(".sale-checkbox:checked");

  if (checkedBoxes.length === 0) {
    showNotification("Please select sales to export", "warning");
    return;
  }

  const selectedIds = Array.from(checkedBoxes).map((cb) => cb.value);
  showNotification(`Exporting ${selectedIds.length} selected sales...`, "info");

  // Simulate bulk export
  setTimeout(() => {
    showNotification("Selected sales exported successfully!", "success");
  }, 2000);
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
window.viewSaleDetails = viewSaleDetails;
window.generateInvoice = generateInvoice;
window.viewReceipt = viewReceipt;
