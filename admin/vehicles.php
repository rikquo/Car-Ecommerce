<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit();
}

if (isset($_POST['name'])) {
    header("Location: /admin/vehicles.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Management - Rev Garage Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/css/admin-dashboard.css">
    <link rel="stylesheet" href="/admin/css/vehicles.css">
</head>

<body>
    <div class="auth-bg-elements">
        <div class="bg-circle bg-circle-1"></div>
        <div class="bg-circle bg-circle-2"></div>
        <div class="bg-circle bg-circle-3"></div>
        <div class="bg-line bg-line-1"></div>
        <div class="bg-line bg-line-2"></div>
    </div>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span>Rev Garage</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-item">
                        <a href="/admin/adminhome.php" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="/admin/vehicles.php" class="nav-link">
                            <i class="fas fa-car"></i>
                            <span>Vehicles</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/customers.php" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Customers</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/orders.php" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/analytics.php" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Analytics</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/settings.php" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <img src="/placeholder.svg?height=40&width=40&text=A" alt="Admin" class="user-avatar">
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_username']); ?></span>
                        <span class="user-role">Administrator</span>
                    </div>
                </div>
                <a href="/pages/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">Vehicle Management</h1>
                </div>

                <div class="header-right">
                    <div class="header-actions">
                        <button class="action-btn" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <button class="action-btn" title="Messages">
                            <i class="fas fa-envelope"></i>
                        </button>
                        <button class="action-btn theme-toggle" title="Toggle Theme">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>

                    <div class="user-profile">
                        <img src="/placeholder.svg?height=40&width=40&text=A" alt="Admin" class="profile-avatar">
                        <span class="profile-name"><?php echo htmlspecialchars($_SESSION['user_username']); ?></span>
                    </div>
                </div>
            </header>

            <!-- Vehicle Management Content -->
            <div class="content-area">
                <!-- Stats Cards -->
                <div class="vehicle-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value">127</h3>
                            <p class="stat-label">Total Vehicles</p>
                            <span class="stat-change positive">+5 this week</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value">89</h3>
                            <p class="stat-label">Available</p>
                            <span class="stat-change positive">70% of total</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value">23</h3>
                            <p class="stat-label">Reserved</p>
                            <span class="stat-change neutral">18% of total</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value">15</h3>
                            <p class="stat-label">Sold</p>
                            <span class="stat-change positive">12% of total</span>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Management Section -->
                <div class="vehicle-management">
                    <div class="section-header">
                        <div class="header-left">
                            <h2 class="section-title">Vehicle Inventory</h2>
                            <p class="section-subtitle">Manage your McLaren collection</p>
                        </div>
                        <div class="header-actions">
                            <button class="primary-btn" id="addVehicleBtn">
                                <i class="fas fa-plus"></i>
                                Add Vehicle
                            </button>
                        </div>
                    </div>

                    <!-- Search and Filter Controls -->
                    <div class="controls-section">
                        <div class="search-controls">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="searchInput" placeholder="Search by model, or specifications...">
                            </div>
                            <button class="filter-btn" id="advancedSearchBtn">
                                <i class="fas fa-filter"></i>
                                Advanced Search
                            </button>
                        </div>

                        <div class="filter-controls">
                            <select class="filter-select" id="seriesFilter">
                                <option value="">All Series</option>
                                <option value="sport">Sport Series</option>
                                <option value="super">Super Series</option>
                                <option value="ultimate">Ultimate Series</option>
                            </select>

                            <select class="filter-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="available">Available</option>
                                <option value="reserved">Reserved</option>
                                <option value="sold">Sold</option>
                                <option value="maintenance">Maintenance</option>
                            </select>


                            <button class="clear-filters-btn" id="clearFiltersBtn">
                                <i class="fas fa-times"></i>
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Advanced Search Panel (Hidden by default) -->
                    <div class="advanced-search-panel" id="advancedSearchPanel">
                        <div class="panel-header">
                            <h3>Advanced Search</h3>
                            <button class="close-panel-btn" id="closeAdvancedSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="panel-content">
                            <div class="search-row">
                                <div class="search-group">
                                    <label>Price Range</label>
                                    <div class="price-range">
                                        <input type="number" placeholder="Min Price" id="minPrice">
                                        <span>to</span>
                                        <input type="number" placeholder="Max Price" id="maxPrice">
                                    </div>
                                </div>
                                <div class="search-group">
                                    <label>Engine Type</label>
                                    <select id="engineFilter">
                                        <option value="">All Engines</option>
                                        <option value="v8">V8</option>
                                        <option value="hybrid">Hybrid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="panel-actions">
                                <button class="secondary-btn" id="resetAdvancedSearch">Reset</button>
                                <button class="primary-btn" id="applyAdvancedSearch">Apply Search</button>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicles Table -->
                    <div class="table-container">
                        <div class="table-header">
                            <div class="table-info">
                                <span class="results-count">Showing <strong id="resultCount">127</strong> vehicles</span>
                            </div>
                            <div class="table-actions">
                                <button class="table-action-btn" id="bulkDeleteBtn" disabled>
                                    <i class="fas fa-trash"></i>
                                    Delete Selected
                                </button>
                                <button class="table-action-btn" id="bulkStatusBtn" disabled>
                                    <i class="fas fa-edit"></i>
                                    Change Status
                                </button>
                            </div>
                        </div>

                        <div class="table-wrapper">
                            <table class="vehicles-table">
                                <thead>
                                    <tr>
                                        <th class="checkbox-col">
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th class="sortable" data-sort="image">
                                            <span>Image</span>
                                        </th>
                                        <th class="sortable" data-sort="model">
                                            <span>Model</span>
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th class="sortable" data-sort="series">
                                            <span>Series</span>
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th class="sortable" data-sort="price">
                                            <span>Price</span>
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th class="sortable" data-sort="status">
                                            <span>Status</span>
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th class="sortable" data-sort="stock">
                                            <span>Stock</span>
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="vehiclesTableBody">
                                    <!-- Sample Data -->
                                    <tr class="vehicle-row">
                                        <td>
                                            <input type="checkbox" class="row-checkbox" data-id="1">
                                        </td>
                                        <td>
                                            <div class="vehicle-image">
                                                <img src="/assets/img/720s.jpg" alt="720S" class="table-image">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="vehicle-info">
                                                <span class="vehicle-name">McLaren 720S</span>

                                            </div>
                                        </td>
                                        <td><span class="series-badge super">Super Series</span></td>
                                        <td class="price-cell">$310,000</td>
                                        <td><span class="status-badge available">Available</span></td>
                                        <td>5</td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn view" title="View Details" onclick="viewVehicle(1)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn edit" title="Edit" onclick="editVehicle(1)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn delete" title="Delete" onclick="deleteVehicle(1)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="action-btn more" title="More Options" onclick="showMoreOptions(1)">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr class="vehicle-row">
                                        <td>
                                            <input type="checkbox" class="row-checkbox" data-id="2">
                                        </td>
                                        <td>
                                            <div class="vehicle-image">
                                                <img src="/placeholder.svg?height=60&width=80" alt="570S" class="table-image">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="vehicle-info">
                                                <span class="vehicle-name">McLaren 570S</span>

                                            </div>
                                        </td>
                                        <td><span class="series-badge sport">Sport Series</span></td>

                                        <td class="price-cell">$215,000</td>
                                        <td><span class="status-badge reserved">Reserved</span></td>
                                        <td>8</td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn view" title="View Details" onclick="viewVehicle(2)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn edit" title="Edit" onclick="editVehicle(2)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn delete" title="Delete" onclick="deleteVehicle(2)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="action-btn more" title="More Options" onclick="showMoreOptions(2)">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr class="vehicle-row">
                                        <td>
                                            <input type="checkbox" class="row-checkbox" data-id="3">
                                        </td>
                                        <td>
                                            <div class="vehicle-image">
                                                <img src="/placeholder.svg?height=60&width=80" alt="P1" class="table-image">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="vehicle-info">
                                                <span class="vehicle-name">McLaren P1</span>

                                            </div>
                                        </td>
                                        <td><span class="series-badge ultimate">Ultimate Series</span></td>

                                        <td class="price-cell">$1,350,000</td>
                                        <td><span class="status-badge sold">Sold</span></td>
                                        <td>2</td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn view" title="View Details" onclick="viewVehicle(3)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn edit" title="Edit" onclick="editVehicle(3)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn delete" title="Delete" onclick="deleteVehicle(3)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="action-btn more" title="More Options" onclick="showMoreOptions(3)">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="table-pagination">
                            <div class="pagination-info">
                                <span>Showing 1-10 of 127 vehicles</span>
                            </div>
                            <div class="pagination-controls">
                                <button class="pagination-btn" disabled>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="pagination-btn active">1</button>
                                <button class="pagination-btn">2</button>
                                <button class="pagination-btn">3</button>
                                <span class="pagination-dots">...</span>
                                <button class="pagination-btn">13</button>
                                <button class="pagination-btn">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php

    if (isset($_SESSION['error'])) {
        echo  $_SESSION['error'];
    }
    ?>
    <!-- Add/Edit Vehicle Modal -->
    <div id="vehicleModal" class="modal active">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Vehicle</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form action="actions/add_vehicles.php" method="POST" enctype="multipart/form-data">



                    <!-- Basic Info Tab -->
                    <div class="tab-content active" id="basic-tab">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Image *</label>
                                <input type="file" name="image" required>
                            </div>
                            <div class="form-group">
                                <label>Model Name *</label>
                                <input type="text" name="model" required placeholder="e.g., McLaren 720S">
                            </div>
                            <div class="form-group">
                                <label>Series *</label>
                                <select name="series" required>
                                    <option value="">Select Series</option>
                                    <option value="1">Sport Series</option>
                                    <option value="2">Super Series</option>
                                    <option value="3">Ultimate Series</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Engine *</label>
                                <input type="text" name="engine" required placeholder="V8, Hybrid, etc.">
                            </div>
                        </div>

                        <div class="form-row">

                            <div class="form-group">
                                <label>Power *</label>
                                <input type="text" name="power" required placeholder="e.g., 710 hp">
                            </div>
                            <div class="form-group">
                                <label>Doors *</label>
                                <input type="text" name="doors" placeholder="e.g., 2 doors">
                            </div>
                        </div>

                        <div class="form-row">

                            <div class="form-group">
                                <label>0-60mph *</label>
                                <input type="text" name="mph" placeholder="e.g., 2.8s">
                            </div>
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" required>
                                    <option value="available" selected>Available</option>
                                    <option value="reserved">Reserved</option>
                                    <option value="sold">Sold</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Price *</label>
                            <input type="number" name="price" placeholder="e.g., $342000">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="4" placeholder="Vehicle description and key features..."></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Stock Quantity *</label>
                                <input type="number" name="stock" required min="0" placeholder="5">
                            </div>
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="secondary-btn" id="cancelVehicle">Cancel</button>
                            <button type="submit">
                                <i class="fas fa-save"></i>
                                Save Vehicle
                            </button>

                        </div>
                    </div>
                </form>
            </div>

            <!-- Vehicle Details Modal -->
            <div id="vehicleDetailsModal" class="modal">
                <div class="modal-content large-modal">
                    <div class="modal-header">
                        <h3 id="detailsModalTitle">Vehicle Details</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="vehicle-details-content" id="vehicleDetailsContent">
                            Vehicle details will be loaded here
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="secondary-btn modal-close">Close</button>
                        <button type="button" class="primary-btn" id="editFromDetails">
                            <i class="fas fa-edit"></i>
                            Edit Vehicle
                        </button>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteModal" class="modal">
                <div class="modal-content small-modal">
                    <div class="modal-header">
                        <h3>Confirm Delete</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="delete-confirmation">
                            <div class="warning-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <p>Are you sure you want to delete this vehicle?</p>
                            <p class="warning-text">This action cannot be undone.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="secondary-btn modal-close">Cancel</button>
                        <button type="button" class="danger-btn" id="confirmDelete">
                            <i class="fas fa-trash"></i>
                            Delete Vehicle
                        </button>
                    </div>
                </div>
            </div>

            <!-- <script src="/admin/js/admin-dashboard.js"></script> -->
            <!-- <script src="/admin/js/vehicles.js"></script>  -->

            <!-- <script>
         const navLinks = document.querySelectorAll(".nav-link")
  const sections = document.querySelectorAll(".content-section")

  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault()

      // Remove active class from all nav items and sections
      navLinks.forEach((nav) => nav.parentElement.classList.remove("active"))
      sections.forEach((section) => section.classList.remove("active"))

      // Add active class to clicked nav item
      this.parentElement.classList.add("active")

      // Show corresponding section
      const sectionId = this.dataset.section + "-section"
      const targetSection = document.getElementById(sectionId)
      if (targetSection) {
        targetSection.classList.add("active")

        // Update page title
        const pageTitle = document.querySelector(".page-title")
        pageTitle.textContent = this.querySelector("span").textContent
      }
    })
  })

  // Sidebar toggle for mobile
  const sidebarToggle = document.querySelector(".sidebar-toggle")
  const sidebar = document.querySelector(".sidebar")

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("active")
    })
  }

  // Theme toggle
  const themeToggle = document.querySelector(".theme-toggle")
  if (themeToggle) {
    themeToggle.addEventListener("click", function () {
      document.body.classList.toggle("light-theme")
      const icon = this.querySelector("i")
      icon.classList.toggle("fa-moon")
      icon.classList.toggle("fa-sun")
    })
  }
    </script> -->
</body>

</html>