// assets/js/admin.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize datepicker if available
    const dateInputs = document.querySelectorAll('.date-input');
    dateInputs.forEach(input => {
        if (typeof flatpickr !== 'undefined') {
            flatpickr(input, {
                dateFormat: "Y-m-d"
            });
        }
    });

    // Handle bulk actions
    const bulkActionForm = document.getElementById('bulk-action-form');
    if (bulkActionForm) {
        bulkActionForm.addEventListener('submit', function(e) {
            const selectedItems = document.querySelectorAll('input[name="selected_items[]"]:checked');
            if (selectedItems.length === 0) {
                e.preventDefault();
                alert('Please select at least one item');
            } else if (!confirm('Are you sure you want to perform this action?')) {
                e.preventDefault();
            }
        });
    }

    // Toggle all checkboxes
    const toggleAll = document.getElementById('toggle-all');
    if (toggleAll) {
        toggleAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Real-time search filtering
    const searchInput = document.getElementById('search-users');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.users-table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Chart initialization for dashboard
    const activityChart = document.getElementById('activity-chart');
    if (activityChart && typeof Chart !== 'undefined') {
        new Chart(activityChart, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'User Activities',
                    data: activityData || [0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#e74c3c',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Handle user status toggle with confirmation
    const statusToggles = document.querySelectorAll('.status-toggle');
    statusToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to change this user\'s status?')) {
                e.preventDefault();
            }
        });
    });

    // Export data functionality
    const exportButtons = document.querySelectorAll('.export-data');
    exportButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.dataset.type;
            const url = `${BASE_URL}/admin/export.php?type=${type}`;
            window.location.href = url;
        });
    });

    // Auto-refresh activity log
    const activityLog = document.querySelector('.activity-log');
    if (activityLog) {
        setInterval(() => {
            fetch(`${BASE_URL}/admin/get-latest-activities.php`)
                .then(response => response.json())
                .then(data => {
                    updateActivityLog(data);
                })
                .catch(error => console.error('Error fetching activities:', error));
        }, 30000); // Refresh every 30 seconds
    }
});

// Utility functions for admin panel
const updateActivityLog = (activities) => {
    const logContainer = document.querySelector('.activity-log tbody');
    if (!logContainer) return;

    activities.forEach(activity => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${formatDate(activity.created_at)}</td>
            <td>${escapeHtml(activity.username)}</td>
            <td>${escapeHtml(activity.activity_type)}</td>
            <td>${escapeHtml(activity.description)}</td>
        `;
        logContainer.insertBefore(row, logContainer.firstChild);
    });

    // Remove old entries to keep the list manageable
    while (logContainer.children.length > 50) {
        logContainer.removeChild(logContainer.lastChild);
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
};

const escapeHtml = (unsafe) => {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
};
