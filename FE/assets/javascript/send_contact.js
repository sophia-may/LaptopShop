// send_contact.js — Submit contact form with client-side validation

document.getElementById("contactForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    clearErrors();

    // Client-side validation
    const name    = this.querySelector('[name="name"]').value.trim();
    const email   = this.querySelector('[name="email"]').value.trim();
    const message = this.querySelector('[name="message"]').value.trim();
    let hasError = false;

    if (!name || name.length < 2) {
        showFieldError('contact_name', 'Name must be at least 2 characters.');
        hasError = true;
    }

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showFieldError('contact_email', 'Please enter a valid email address.');
        hasError = true;
    }

    if (!message || message.length < 10) {
        showFieldError('contact_message', 'Message must be at least 10 characters.');
        hasError = true;
    }

    if (hasError) return;

    // Disable button while sending
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';

    try {
        const formData = new FormData(this);
        const res = await fetch("controllers/ContactController.php?action=create", {
            method: "POST",
            body: formData
        });
        const result = await res.json();

        if (result.success) {
            showFormAlert('success', result.message || 'Message sent successfully!');
            this.reset();
        } else {
            showFormAlert('danger', result.message || 'Failed to send message.');
        }
    } catch (err) {
        showFormAlert('danger', 'Network error. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
});

function showFieldError(fieldId, msg) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    field.classList.add('is-invalid');
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    feedback.textContent = msg;
    field.parentNode.appendChild(feedback);
}

function clearErrors() {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    const existingAlert = document.getElementById('contactFormAlert');
    if (existingAlert) existingAlert.remove();
}

function showFormAlert(type, msg) {
    const existingAlert = document.getElementById('contactFormAlert');
    if (existingAlert) existingAlert.remove();

    const alert = document.createElement('div');
    alert.id = 'contactFormAlert';
    alert.className = `alert alert-${type} mt-3`;
    alert.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${msg}`;
    
    const form = document.getElementById('contactForm');
    form.appendChild(alert);

    if (type === 'success') {
        setTimeout(() => alert.remove(), 5000);
    }
}
