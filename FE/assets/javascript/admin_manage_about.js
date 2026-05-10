// admin_manage_about.js

document.addEventListener('DOMContentLoaded', function() {
    loadAboutSettings();
    setupFormHandler();
});

// Load about page settings
async function loadAboutSettings() {
    try {
        const response = await fetch('ajax/settings_handler.php?action=get_by_group&group=about');
        const result = await response.json();
        
        if (result.success) {
            populateFields(result.data);
        } else {
            showToast('error', result.message || 'Error loading data');
        }
    } catch (error) {
        console.error('Error loading about settings:', error);
        showToast('error', 'Server connection error');
    }
}

// Populate form fields with loaded data
function populateFields(settings) {
    for (const key in settings) {
        // Field ID is about_key (e.g., "about_title")
        const fieldId = 'about_' + key;
        const field = document.getElementById(fieldId);
        
        if (field) {
            field.value = settings[key] || '';
        }
    }
}

// Setup form submit handler
function setupFormHandler() {
    const form = document.getElementById('form-about');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            await saveAboutSettings(form);
        });
    }
}

// Save about page settings
async function saveAboutSettings(form) {
    const formData = new FormData(form);
    const group = form.dataset.group; // Get group from data-group attribute
    const settings = {};
    
    // Build settings object with group prefix: about_key
    for (const [key, value] of formData.entries()) {
        settings[group + '_' + key] = value;
    }
    
    try {
        const response = await fetch('ajax/settings_handler.php?action=save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ settings })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('success', result.message || 'Saved successfully!');
        } else {
            showToast('error', result.message || 'Error saving data');
        }
    } catch (error) {
        console.error('Error saving about settings:', error);
        showToast('error', 'Server connection error');
    }
}

// Show toast notification
function showToast(type, message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    
    toast.innerHTML = `
        <div class="d-flex">
            <div>
                <i class="ti ti-${type === 'success' ? 'check' : 'alert-circle'} me-2"></i>
            </div>
            <div>
                <h4 class="alert-title">${type === 'success' ? 'Success' : 'Error'}!</h4>
                <div class="text-secondary">${message}</div>
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
