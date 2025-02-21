// assets/js/main.js

document.addEventListener('DOMContentLoaded', function() {
    // File upload preview
    const fileInput = document.querySelector('.file-input');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file selected';
            document.querySelector('.file-name').textContent = fileName;
        });
    }

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    showError(field, 'This field is required');
                } else {
                    clearError(field);
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    });
});

// Utility functions
function showError(field, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
    field.classList.add('error');
}

function clearError(field) {
    const errorDiv = field.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
    field.classList.remove('error');
}

// AJAX functions for dynamic content loading
async function loadContent(url, targetElement) {
    try {
        const response = await fetch(url);
        const data = await response.text();
        document.querySelector(targetElement).innerHTML = data;
    } catch (error) {
        console.error('Error loading content:', error);
    }
}

// File upload progress
function updateProgress(evt) {
    if (evt.lengthComputable) {
        const percentComplete = (evt.loaded / evt.total) * 100;
        document.querySelector('.progress-bar').style.width = percentComplete + '%';
    }
}
