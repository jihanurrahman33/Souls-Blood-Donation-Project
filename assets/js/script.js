// Blood Donation Website JavaScript

document.addEventListener("DOMContentLoaded", function () {
  // Initialize all components
  initializeFormValidation();
  initializePasswordToggles();
  initializeCharacterCounters();
  initializeTooltips();
  initializeAnimations();
  initializeAPIHandlers();
});

// Form Validation
function initializeFormValidation() {
  const forms = document.querySelectorAll("form[data-validate]");

  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!validateForm(form)) {
        e.preventDefault();
      }
    });
  });
}

function validateForm(form) {
  let isValid = true;
  const inputs = form.querySelectorAll(
    "input[required], select[required], textarea[required]"
  );

  inputs.forEach((input) => {
    if (!validateField(input)) {
      isValid = false;
    }
  });

  return isValid;
}

function validateField(field) {
  const value = field.value.trim();
  const type = field.type;
  const name = field.name;

  // Clear previous errors
  clearFieldError(field);

  // Required validation
  if (field.hasAttribute("required") && !value) {
    showFieldError(field, `${getFieldLabel(field)} is required`);
    return false;
  }

  // Type-specific validation
  switch (type) {
    case "email":
      if (value && !isValidEmail(value)) {
        showFieldError(field, "Please enter a valid email address");
        return false;
      }
      break;
    case "tel":
      if (value && !isValidPhone(value)) {
        showFieldError(field, "Please enter a valid phone number");
        return false;
      }
      break;
    case "date":
      if (value && !isValidDate(value)) {
        showFieldError(field, "Please enter a valid date");
        return false;
      }
      break;
  }

  // Custom validation based on field name
  switch (name) {
    case "password":
      if (value && value.length < 6) {
        showFieldError(field, "Password must be at least 6 characters long");
        return false;
      }
      break;
    case "confirm_password":
      const password = form.querySelector('input[name="password"]');
      if (value && password && value !== password.value) {
        showFieldError(field, "Passwords do not match");
        return false;
      }
      break;
  }

  return true;
}

function showFieldError(field, message) {
  field.classList.add("is-invalid");
  const errorElement = field.parentNode.querySelector(".invalid-feedback");
  if (errorElement) {
    errorElement.textContent = message;
  }
}

function clearFieldError(field) {
  field.classList.remove("is-invalid");
  const errorElement = field.parentNode.querySelector(".invalid-feedback");
  if (errorElement) {
    errorElement.textContent = "";
  }
}

function getFieldLabel(field) {
  const label = field.parentNode.querySelector("label");
  return label ? label.textContent.replace(/[*\s]+$/, "") : "This field";
}

// Validation Helpers
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function isValidPhone(phone) {
  const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
  return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ""));
}

function isValidDate(date) {
  const dateObj = new Date(date);
  return dateObj instanceof Date && !isNaN(dateObj);
}

// Password Toggle
function initializePasswordToggles() {
  const toggleButtons = document.querySelectorAll('[data-toggle="password"]');

  toggleButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const input = this.parentNode.querySelector("input");
      const icon = this.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    });
  });
}

// Character Counters
function initializeCharacterCounters() {
  const textareas = document.querySelectorAll("textarea[maxlength]");

  textareas.forEach((textarea) => {
    const maxLength = parseInt(textarea.getAttribute("maxlength"));
    const counter = textarea.parentNode.querySelector(".form-text");

    if (counter) {
      updateCharacterCount(textarea, maxLength, counter);

      textarea.addEventListener("input", function () {
        updateCharacterCount(this, maxLength, counter);
      });
    }
  });
}

function updateCharacterCount(field, maxLength, counter) {
  const remaining = maxLength - field.value.length;
  counter.textContent = `${remaining} characters remaining`;

  if (remaining < maxLength * 0.1) {
    counter.classList.add("text-warning");
  } else {
    counter.classList.remove("text-warning");
  }
}

// Tooltips
function initializeTooltips() {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
}

// Animations
function initializeAnimations() {
  const animatedElements = document.querySelectorAll(".fade-in-up");

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("fade-in-up");
      }
    });
  });

  animatedElements.forEach((el) => observer.observe(el));
}

// API Handlers
function initializeAPIHandlers() {
  // Handle API form submissions
  const apiForms = document.querySelectorAll("form[data-api]");

  apiForms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      submitFormAPI(form);
    });
  });
}

async function submitFormAPI(form) {
  const formData = new FormData(form);
  const submitButton = form.querySelector('button[type="submit"]');
  const originalText = submitButton.innerHTML;

  try {
    // Show loading state
    submitButton.disabled = true;
    submitButton.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

    const response = await fetch(form.action, {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (response.ok) {
      showNotification("success", result.message || "Success!");
      form.reset();
    } else {
      showNotification("error", result.error || "An error occurred");
    }
  } catch (error) {
    showNotification("error", "Network error. Please try again.");
  } finally {
    // Restore button state
    submitButton.disabled = false;
    submitButton.innerHTML = originalText;
  }
}

// Notifications
function showNotification(type, message) {
  const alertClass = type === "success" ? "alert-success" : "alert-danger";
  const icon = type === "success" ? "fa-check-circle" : "fa-exclamation-circle";

  const alert = document.createElement("div");
  alert.className = `alert ${alertClass} alert-dismissible fade show`;
  alert.innerHTML = `
        <i class="fas ${icon} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

  // Insert at the top of the main content
  const main = document.querySelector("main");
  if (main) {
    main.insertBefore(alert, main.firstChild);
  }

  // Auto-dismiss after 5 seconds
  setTimeout(() => {
    if (alert.parentNode) {
      alert.remove();
    }
  }, 5000);
}

// Utility Functions
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

function throttle(func, limit) {
  let inThrottle;
  return function () {
    const args = arguments;
    const context = this;
    if (!inThrottle) {
      func.apply(context, args);
      inThrottle = true;
      setTimeout(() => (inThrottle = false), limit);
    }
  };
}

// Search functionality
function initializeSearch() {
  const searchInput = document.querySelector("#searchInput");
  if (searchInput) {
    const debouncedSearch = debounce(function (value) {
      performSearch(value);
    }, 300);

    searchInput.addEventListener("input", function () {
      debouncedSearch(this.value);
    });
  }
}

async function performSearch(query) {
  if (query.length < 2) return;

  try {
    const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
    const results = await response.json();
    displaySearchResults(results);
  } catch (error) {
    console.error("Search error:", error);
  }
}

function displaySearchResults(results) {
  const resultsContainer = document.querySelector("#searchResults");
  if (!resultsContainer) return;

  if (results.length === 0) {
    resultsContainer.innerHTML = '<p class="text-muted">No results found</p>';
    return;
  }

  const html = results
    .map(
      (result) => `
        <div class="search-result-item p-2 border-bottom">
            <h6 class="mb-1">${result.title}</h6>
            <small class="text-muted">${result.description}</small>
        </div>
    `
    )
    .join("");

  resultsContainer.innerHTML = html;
}

// Export functions for global use
window.BloodDonationApp = {
  showNotification,
  validateForm,
  submitFormAPI,
  performSearch,
};
