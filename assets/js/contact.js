/**
 * Swamini Photography - Contact Form & Booking Handler
 */

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('enquiryForm');
  if (!form) return;

  const submitBtn = form.querySelector('button[type="submit"]');
  const btnOriginalText = submitBtn ? submitBtn.innerHTML : 'Send Enquiry';

  // Helper: Toast Notifications
  function showToast(message, type = 'success') {
    let container = document.querySelector('.toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
      <div>
        <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: 2px;">
          ${type === 'success' ? 'Enquiry Sent' : 'Notice'}
        </div>
        <div style="font-size: 0.8125rem; opacity: 0.9;">${message}</div>
      </div>
    `;

    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);

    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, 5000);
  }

  // Clear previous validation errors
  function clearErrors() {
    form.querySelectorAll('.form-group').forEach(group => {
      group.classList.remove('has-error');
      const err = group.querySelector('.form-error');
      if (err) err.textContent = '';
    });
  }

  // Set field error
  function setError(fieldName, message) {
    const input = form.querySelector(`[name="${fieldName}"]`);
    if (input) {
      const group = input.closest('.form-group');
      if (group) {
        group.classList.add('has-error');
        let err = group.querySelector('.form-error');
        if (!err) {
          err = document.createElement('div');
          err.className = 'form-error';
          group.appendChild(err);
        }
        err.textContent = message;
      }
    }
  }

  // Handle Form Submission
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();

    // Client-side quick check
    const name = form.querySelector('[name="name"]').value.trim();
    const phone = form.querySelector('[name="phone"]').value.trim();
    const service = form.querySelector('[name="service"]').value.trim();
    let hasClientError = false;

    if (!name || name.length < 2) {
      setError('name', 'Please enter your full name.');
      hasClientError = true;
    }

    if (!phone || phone.length < 10) {
      setError('phone', 'Please enter a valid 10-digit phone number.');
      hasClientError = true;
    }

    if (!service) {
      setError('service', 'Please select a service.');
      hasClientError = true;
    }

    if (hasClientError) return;

    // Loading State
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = `
        <svg style="animation: spin 1s linear infinite; width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
          <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
        </svg>
        <span>Sending Enquiry...</span>
      `;
    }

    const formData = new FormData(form);

    try {
      const response = await fetch(form.action || 'api/contact.php', {
        method: 'POST',
        body: formData,
        headers: {
          'Accept': 'application/json'
        }
      });

      const result = await response.json();

      // Update CSRF token if returned
      if (result.new_csrf_token) {
        const csrfInput = form.querySelector('[name="csrf_token"]');
        if (csrfInput) csrfInput.value = result.new_csrf_token;
      }

      if (response.ok && result.success) {
        showToast(result.message, 'success');
        form.reset();
      } else {
        if (result.errors) {
          Object.entries(result.errors).forEach(([field, msg]) => {
            setError(field, msg);
          });
        }
        showToast(result.message || 'There was an issue submitting your enquiry.', 'error');
      }
    } catch (err) {
      showToast('Network error or server unreachable. Please message us on WhatsApp.', 'error');
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = btnOriginalText;
      }
    }
  });

  // Global CTA Service Select Pre-fill Hooks
  document.querySelectorAll('[data-select-service]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const serviceVal = btn.getAttribute('data-select-service');
      const serviceSelect = document.getElementById('serviceSelect');
      if (serviceSelect && serviceVal) {
        serviceSelect.value = serviceVal;
      }
    });
  });
});
