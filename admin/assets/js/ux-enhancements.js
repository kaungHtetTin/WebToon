/**
 * UX Enhancements for Admin Dashboard
 * Toast notifications, form validation, loading states, and more
 */

(function() {
  "use strict";

  /**
   * Toast Notification System
   */
  class ToastNotification {
    constructor() {
      this.container = this.createContainer();
    }

    createContainer() {
      let container = document.getElementById('toast-container');
      if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
      }
      return container;
    }

    show(message, type = 'info', duration = 4000) {
      const toast = document.createElement('div');
      toast.className = `toast toast-${type}`;
      
      const icon = this.getIcon(type);
      toast.innerHTML = `
        <div class="toast-content">
          <div class="toast-icon">${icon}</div>
          <div class="toast-message">${message}</div>
          <button class="toast-close" onclick="this.parentElement.parentElement.remove()">
            <i class="bi bi-x"></i>
          </button>
        </div>
        <div class="toast-progress"></div>
      `;

      this.container.appendChild(toast);

      // Trigger animation
      setTimeout(() => toast.classList.add('show'), 10);

      // Auto remove
      if (duration > 0) {
        const progressBar = toast.querySelector('.toast-progress');
        progressBar.style.animation = `toast-progress ${duration}ms linear`;
        
        setTimeout(() => {
          toast.classList.remove('show');
          setTimeout(() => toast.remove(), 300);
        }, duration);
      }

      return toast;
    }

    getIcon(type) {
      const icons = {
        success: '<i class="bi bi-check-circle-fill"></i>',
        error: '<i class="bi bi-x-circle-fill"></i>',
        warning: '<i class="bi bi-exclamation-triangle-fill"></i>',
        info: '<i class="bi bi-info-circle-fill"></i>'
      };
      return icons[type] || icons.info;
    }

    success(message, duration) {
      return this.show(message, 'success', duration);
    }

    error(message, duration) {
      return this.show(message, 'error', duration);
    }

    warning(message, duration) {
      return this.show(message, 'warning', duration);
    }

    info(message, duration) {
      return this.show(message, 'info', duration);
    }
  }

  // Global toast instance
  window.Toast = new ToastNotification();

  /**
   * Enhanced Confirmation Dialog
   */
  window.confirmAction = function(message, callback, options = {}) {
    const {
      title = 'Confirm Action',
      confirmText = 'Confirm',
      cancelText = 'Cancel',
      type = 'warning',
      danger = false
    } = options;

    const modal = document.createElement('div');
    modal.className = 'confirm-modal-overlay';
    modal.innerHTML = `
      <div class="confirm-modal">
        <div class="confirm-modal-header">
          <h5>${title}</h5>
          <button class="confirm-modal-close" onclick="this.closest('.confirm-modal-overlay').remove()">
            <i class="bi bi-x"></i>
          </button>
        </div>
        <div class="confirm-modal-body">
          <div class="confirm-modal-icon confirm-modal-icon-${type}">
            ${type === 'danger' || danger ? '<i class="bi bi-exclamation-triangle-fill"></i>' : 
              type === 'warning' ? '<i class="bi bi-exclamation-triangle-fill"></i>' :
              '<i class="bi bi-question-circle-fill"></i>'}
          </div>
          <p>${message}</p>
        </div>
        <div class="confirm-modal-footer">
          <button class="btn btn-secondary confirm-cancel">${cancelText}</button>
          <button class="btn ${danger ? 'btn-danger' : 'btn-primary'} confirm-ok">${confirmText}</button>
        </div>
      </div>
    `;

    document.body.appendChild(modal);
    modal.style.display = 'flex';

    const overlay = modal;
    const cancelBtn = modal.querySelector('.confirm-cancel');
    const okBtn = modal.querySelector('.confirm-ok');

    const close = () => {
      overlay.style.opacity = '0';
      setTimeout(() => overlay.remove(), 200);
    };

    cancelBtn.onclick = close;
    okBtn.onclick = () => {
      if (callback) callback();
      close();
    };

    overlay.onclick = (e) => {
      if (e.target === overlay) close();
    };
  };

  /**
   * Enhanced Delete Confirmation
   */
  window.confirmDelete = function(message, callback) {
    return window.confirmAction(
      message || 'Are you sure you want to delete this item? This action cannot be undone.',
      callback,
      {
        title: 'Delete Confirmation',
        confirmText: 'Delete',
        cancelText: 'Cancel',
        type: 'danger',
        danger: true
      }
    );
  };

  /**
   * Loading State Manager
   */
  window.showLoading = function(element, text = 'Loading...') {
    if (typeof element === 'string') {
      element = document.querySelector(element);
    }
    
    if (!element) return;

    const loader = document.createElement('div');
    loader.className = 'loading-overlay';
    loader.innerHTML = `
      <div class="loading-spinner">
        <div class="spinner"></div>
        <p>${text}</p>
      </div>
    `;

    element.style.position = 'relative';
    element.appendChild(loader);
    
    return {
      remove: () => {
        loader.style.opacity = '0';
        setTimeout(() => loader.remove(), 200);
      }
    };
  };

  /**
   * Enhanced Form Validation
   */
  function enhanceFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
      const inputs = form.querySelectorAll('input, textarea, select');
      
      inputs.forEach(input => {
        // Real-time validation
        input.addEventListener('blur', function() {
          validateField(this);
        });

        input.addEventListener('input', function() {
          if (this.classList.contains('is-invalid')) {
            validateField(this);
          }
        });
      });

      form.addEventListener('submit', function(e) {
        let isValid = true;
        inputs.forEach(input => {
          if (!validateField(input)) {
            isValid = false;
          }
        });

        if (!isValid) {
          e.preventDefault();
          e.stopPropagation();
          Toast.error('Please fix the errors in the form before submitting.');
          
          // Scroll to first error
          const firstError = form.querySelector('.is-invalid');
          if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
          }
        }
      });
    });
  }

  function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    let isValid = true;
    let errorMessage = '';

    // Remove previous validation
    field.classList.remove('is-valid', 'is-invalid');
    const feedback = field.parentElement.querySelector('.invalid-feedback');
    if (feedback) feedback.remove();

    // Required validation
    if (field.hasAttribute('required') && !value) {
      isValid = false;
      errorMessage = field.getAttribute('data-error') || 'This field is required.';
    }

    // Email validation
    if (type === 'email' && value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        isValid = false;
        errorMessage = 'Please enter a valid email address.';
      }
    }

    // Number validation
    if (type === 'number' && value) {
      if (isNaN(value)) {
        isValid = false;
        errorMessage = 'Please enter a valid number.';
      }
    }

    // Min/Max length
    const minLength = field.getAttribute('minlength');
    const maxLength = field.getAttribute('maxlength');
    if (minLength && value.length < parseInt(minLength)) {
      isValid = false;
      errorMessage = `Minimum length is ${minLength} characters.`;
    }
    if (maxLength && value.length > parseInt(maxLength)) {
      isValid = false;
      errorMessage = `Maximum length is ${maxLength} characters.`;
    }

    // Apply validation state
    if (field.hasAttribute('required') || value) {
      field.classList.add(isValid ? 'is-valid' : 'is-invalid');
      
      if (!isValid && errorMessage) {
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = errorMessage;
        field.parentElement.appendChild(feedback);
      }
    }

    return isValid;
  }

  /**
   * Enhanced Table Interactions
   */
  function enhanceTables() {
    const tables = document.querySelectorAll('.table');
    
    tables.forEach(table => {
      // Add row hover effects
      const rows = table.querySelectorAll('tbody tr');
      rows.forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function(e) {
          // Don't trigger if clicking on a button or link
          if (!e.target.closest('a, button')) {
            // Could add row selection or detail view here
          }
        });
      });

      // Add empty state
      if (rows.length === 0) {
        const tbody = table.querySelector('tbody');
        if (tbody) {
          tbody.innerHTML = `
            <tr>
              <td colspan="100%" class="text-center py-5">
                <div class="empty-state">
                  <i class="bi bi-inbox empty-state-icon"></i>
                  <h5>No data available</h5>
                  <p class="text-muted">There are no items to display.</p>
                </div>
              </td>
            </tr>
          `;
        }
      }
    });
  }

  /**
   * Enhanced Delete Links
   */
  function enhanceDeleteLinks() {
    document.addEventListener('click', function(e) {
      const deleteLink = e.target.closest('a[onclick*="confirm"], a[href*="delete"]');
      
      if (deleteLink && deleteLink.getAttribute('data-confirm') !== 'false') {
        e.preventDefault();
        
        const href = deleteLink.getAttribute('href');
        const message = deleteLink.getAttribute('data-message') || 
                       deleteLink.getAttribute('title') ||
                       'Are you sure you want to delete this item?';
        
        window.confirmDelete(message, function() {
          if (href && href !== '#') {
            window.location.href = href;
          } else if (deleteLink.onclick) {
            // Execute original onclick
            const onclickAttr = deleteLink.getAttribute('onclick');
            if (onclickAttr) {
              eval(onclickAttr);
            }
          }
        });
      }
    });
  }

  /**
   * Auto-dismiss alerts
   */
  function autoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert[data-auto-dismiss]');
    alerts.forEach(alert => {
      const duration = parseInt(alert.getAttribute('data-auto-dismiss')) || 5000;
      setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
      }, duration);
    });
  }

  /**
   * Initialize all enhancements
   */
  function init() {
    enhanceFormValidation();
    enhanceTables();
    enhanceDeleteLinks();
    autoDismissAlerts();

    // Show success/error messages from PHP
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success')) {
      Toast.success(decodeURIComponent(urlParams.get('success')) || 'Operation completed successfully!');
      // Clean URL
      const cleanUrl = window.location.pathname;
      window.history.replaceState({}, document.title, cleanUrl);
    }
    if (urlParams.get('error')) {
      Toast.error(decodeURIComponent(urlParams.get('error')) || 'An error occurred.');
      // Clean URL
      const cleanUrl = window.location.pathname;
      window.history.replaceState({}, document.title, cleanUrl);
    }

    // Show PHP session messages if any
    const phpMessages = document.querySelectorAll('.alert-message');
    phpMessages.forEach(alert => {
      const type = alert.classList.contains('alert-success') ? 'success' :
                   alert.classList.contains('alert-danger') ? 'error' :
                   alert.classList.contains('alert-warning') ? 'warning' : 'info';
      const message = alert.textContent.trim();
      if (message) {
        Toast[type](message);
        alert.remove();
      }
    });
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();

