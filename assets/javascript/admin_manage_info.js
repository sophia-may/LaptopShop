// admin_manage_info.js

document.addEventListener('DOMContentLoaded', function() {
    loadAllSettings();
    setupFormHandlers();
    setupImageUploadHandlers();
});

// Load all settings when page loads
async function loadAllSettings() {
    try {
        const response = await fetch('ajax/settings_handler.php?action=get');
        const result = await response.json();
        
        if (result.success) {
            populateFields(result.data);
        } else {
            showToast('error', result.message || 'Error loading data');
        }
    } catch (error) {
        console.error('Error loading settings:', error);
        showToast('error', 'Server connection error');
    }
}

// Populate form fields with loaded data (JSON structure)
function populateFields(settings) {
    // Loop through each group in settings
    for (const group in settings) {
        const groupSettings = settings[group];
        
        // Loop through each setting in the group
        for (const key in groupSettings) {
            // Create field ID: group_key (e.g., "general_site_name")
            const fieldId = group + '_' + key;
            const field = document.getElementById(fieldId);
            
            if (field) {
                field.value = groupSettings[key] || '';
            }
        }
    }
}

// Setup form submit handlers
function setupFormHandlers() {
    const forms = ['form-general', 'form-header', 'form-home', 'form-contact', 'form-footer', 'form-shop'];
    
    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                await saveSettings(form);
            });
        }
    });
}

// Setup image upload handlers (no longer needed - uploads happen on form submit)
function setupImageUploadHandlers() {
    // No automatic upload - files will be uploaded when form is submitted
}


// Save settings from a form
async function saveSettings(form) {
    const formData = new FormData(form);
    const group = form.dataset.group;
    const settings = {};
    
    // Check if there are any image files to upload first
    const imageFields = [
        { fileInputId: 'general_site_logo_file', fieldName: 'general_site_logo', settingKey: 'site_logo', group: 'general' },
        { fileInputId: 'home_banner_1_image_file', fieldName: 'home_banner_1_image', settingKey: 'banner_1_image', group: 'home' },
        { fileInputId: 'home_banner_2_image_file', fieldName: 'home_banner_2_image', settingKey: 'banner_2_image', group: 'home' }
    ];
    
    // Upload images first if any
    for (const field of imageFields) {
        if (field.group === group) {
            const fileInput = document.getElementById(field.fileInputId);
            if (fileInput && fileInput.files.length > 0) {
                showToast('info', 'Uploading image...');
                
                const uploadFormData = new FormData();
                uploadFormData.append('image', fileInput.files[0]);
                uploadFormData.append('field_name', field.fieldName);
                
                try {
                    const uploadResponse = await fetch('ajax/image_upload_handler.php', {
                        method: 'POST',
                        body: uploadFormData
                    });
                    
                    const responseText = await uploadResponse.text();
                    const uploadResult = JSON.parse(responseText);
                    
                    if (uploadResult.success) {
                        // Add uploaded image path to settings
                        settings[group + '_' + field.settingKey] = uploadResult.new_path;
                        // Clear file input
                        fileInput.value = '';
                    } else {
                        showToast('error', uploadResult.message);
                        return;
                    }
                } catch (error) {
                    showToast('error', 'Image upload failed: ' + error.message);
                    return;
                }
            }
        }
    }
    
    // Build settings object from text inputs
    for (const [key, value] of formData.entries()) {
        // Skip file inputs
        if (!key.includes('_file')) {
            settings[group + '_' + key] = value;
        }
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
        console.error('Error saving settings:', error);
        showToast('error', 'Server connection error');
    }
}

// Show toast notification
function showToast(type, message) {
    // Remove existing toasts
    document.querySelectorAll('.alert-toast').forEach(t => t.remove());
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'info' ? 'info' : 'danger'} alert-dismissible alert-toast`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    
    const iconMap = {
        'success': 'check',
        'error': 'alert-circle',
        'info': 'info-circle'
    };
    
    const titleMap = {
        'success': 'Success',
        'error': 'Error',
        'info': 'Info'
    };
    
    toast.innerHTML = `
        <div class="d-flex">
            <div>
                <i class="ti ti-${iconMap[type] || 'info-circle'} me-2"></i>
            </div>
            <div>
                <h4 class="alert-title">${titleMap[type] || 'Notification'}!</h4>
                <div class="text-secondary">${message}</div>
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds (except for info which stays longer)
    setTimeout(() => {
        toast.remove();
    }, type === 'info' ? 5000 : 3000);
}
