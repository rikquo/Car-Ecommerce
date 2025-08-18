// Search functionality
document.getElementById("searchInput").addEventListener("input", function () {
  const searchTerm = this.value;
  const roleFilter = document.getElementById("roleFilter").value;
  filterUsers(searchTerm, roleFilter);
});

document.getElementById("roleFilter").addEventListener("change", function () {
  const roleFilter = this.value;
  const searchTerm = document.getElementById("searchInput").value;
  filterUsers(searchTerm, roleFilter);
});

function filterUsers(search, role) {
  const url = new URL(window.location);
  if (search) {
    url.searchParams.set("search", search);
  } else {
    url.searchParams.delete("search");
  }
  if (role) {
    url.searchParams.set("role", role);
  } else {
    url.searchParams.delete("role");
  }
  window.location.href = url.toString();
}

// Modal functions
function openAddUserModal() {
  document.getElementById("modalTitle").textContent = "Add New User";
  document.getElementById("formAction").value = "add_user";
  document.getElementById("userId").value = "";
  document.getElementById("userForm").reset();
  document.getElementById("passwordHelp").textContent =
    "Required for new users";
  document.getElementById("password").required = true;
  document.getElementById("userModal").classList.add("active");
}

function editUser(userId) {
  // Fetch user data
  fetch("users.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `action=get_user&user_id=${userId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("modalTitle").textContent = "Edit User";
        document.getElementById("formAction").value = "update_user";
        document.getElementById("userId").value = data.user.user_id;
        document.getElementById("username").value = data.user.username;
        document.getElementById("email").value = data.user.email;
        document.getElementById("role").value = data.user.role;
        document.getElementById("passwordHelp").textContent =
          "Leave blank to keep current password";
        document.getElementById("password").required = false;
        document.getElementById("userModal").classList.add("active");
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      showNotification("Error fetching user data", "error");
    });
}

function viewUser(userId) {
  fetch("users.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `action=get_user&user_id=${userId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const user = data.user;
        const profilePic =
          user.profile_picture ||
          `/placeholder.svg?height=80&width=80&text=${user.username.charAt(0)}`;

        document.getElementById("userDetails").innerHTML = `
                        <div style="text-align: center; margin-bottom: 2rem;">
                            <img src="${profilePic}" alt="${
          user.username
        }" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                        </div>
                        <div style="display: grid; gap: 1rem;">
                            <div>
                                <strong style="color: rgba(255,255,255,0.7);">Username:</strong>
                                <div style="color: white; margin-top: 0.25rem;">${
                                  user.username
                                }</div>
                            </div>
                            <div>
                                <strong style="color: rgba(255,255,255,0.7);">Email:</strong>
                                <div style="color: white; margin-top: 0.25rem;">${
                                  user.email
                                }</div>
                            </div>
                            <div>
                                <strong style="color: rgba(255,255,255,0.7);">Role:</strong>
                                <div style="color: white; margin-top: 0.25rem;">
                                    <span class="role-badge ${user.role}">${
          user.role.charAt(0).toUpperCase() + user.role.slice(1)
        }</span>
                                </div>
                            </div>
                            <div>
                                <strong style="color: rgba(255,255,255,0.7);">Created:</strong>
                                <div style="color: white; margin-top: 0.25rem;">${new Date(
                                  user.created_at
                                ).toLocaleDateString()}</div>
                            </div>
                        </div>
                    `;
        document.getElementById("viewUserModal").classList.add("active");
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      showNotification("Error fetching user data", "error");
    });
}

function deleteUser(userId) {
  if (
    confirm(
      "Are you sure you want to delete this user? This action cannot be undone."
    )
  ) {
    fetch("users.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=delete_user&user_id=${userId}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showNotification(data.message, "success");
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          showNotification(data.message, "error");
        }
      })
      .catch((error) => {
        showNotification("Error deleting user", "error");
      });
  }
}

function closeUserModal() {
  document.getElementById("userModal").classList.remove("active");
}

function closeViewUserModal() {
  document.getElementById("viewUserModal").classList.remove("active");
}

// Form submission
document.getElementById("userForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("users.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification(data.message, "success");
        closeUserModal();
        setTimeout(() => {
          location.reload();
        }, 1500);
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      showNotification("Error saving user", "error");
    });
});

function showNotification(message, type) {
  const notification = document.createElement("div");
  notification.className = `notification ${type}`;
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

// Sidebar toggle
document
  .querySelector(".sidebar-toggle")
  .addEventListener("click", function () {
    document.querySelector(".sidebar").classList.toggle("active");
  });

// Theme toggle
document.querySelector(".theme-toggle").addEventListener("click", function () {
  document.body.classList.toggle("light-theme");
  const icon = this.querySelector("i");
  icon.classList.toggle("fa-moon");
  icon.classList.toggle("fa-sun");
});
