// Early Warning Academic Monitoring System - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initModals();
    initDropdowns();
    initTabs();
    initFormValidation();
    initDataTables();
    initCharts();
    initRiskCalculation();
    initFileUpload();
    initNotifications();
});

// Sidebar Toggle for Mobile
function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
    
    // Close sidebar on outside click (mobile)
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !toggleBtn?.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
}

// Modal Management
function initModals() {
    const modals = document.querySelectorAll('.modal-overlay');
    
    modals.forEach(modal => {
        const closeBtn = modal.querySelector('.modal-close');
        const cancelBtn = modal.querySelector('.btn-cancel');
        
        const closeModal = () => {
            modal.classList.remove('active');
        };
        
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    });
    
    // Open modal with data attributes
    document.querySelectorAll('[data-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modalId = btn.dataset.modal;
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('active');
        });
    });
}

// Dropdown Menus
function initDropdowns() {
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        
        if (toggle) {
            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('active');
            });
        }
    });
    
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown.active').forEach(d => d.classList.remove('active'));
    });
}

// Tabs
function initTabs() {
    document.querySelectorAll('.tabs').forEach(tabContainer => {
        const tabs = tabContainer.querySelectorAll('.tab');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Hide all tab content
                const tabContent = tabContainer.parentElement.querySelectorAll('.tab-content');
                tabContent.forEach(content => {
                    content.classList.remove('active');
                });
                
                // Show target tab content
                const targetContent = document.getElementById(target);
                if (targetContent) targetContent.classList.add('active');
            });
        });
    });
}

// Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showMessage(form, 'Please fill in all required fields', 'error');
            }
        });
    });
}

// Data Tables with Search and Pagination
function initDataTables() {
    document.querySelectorAll('.data-table').forEach(table => {
        const searchInput = table.dataset.search;
        const filterSelect = table.dataset.filter;
        
        if (searchInput) {
            const input = document.getElementById(searchInput);
            if (input) {
                input.addEventListener('input', () => filterTable(table, input.value));
            }
        }
    });
}

function filterTable(table, searchTerm) {
    const rows = table.querySelectorAll('tbody tr');
    searchTerm = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// Charts (using Chart.js if available)
function initCharts() {
    if (typeof Chart === 'undefined') return;
    
    // Risk Distribution Chart
    const riskCtx = document.getElementById('riskChart');
    if (riskCtx) {
        new Chart(riskCtx, {
            type: 'doughnut',
            data: {
                labels: ['Low Risk', 'Moderate Risk', 'High Risk'],
                datasets: [{
                    data: [65, 25, 10],
                    backgroundColor: ['#38a169', '#d69e2e', '#e53e3e'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    // Grade Distribution Chart
    const gradeCtx = document.getElementById('gradeChart');
    if (gradeCtx) {
        new Chart(gradeCtx, {
            type: 'bar',
            data: {
                labels: ['A', 'B', 'C', 'D', 'F'],
                datasets: [{
                    label: 'Students',
                    data: [12, 19, 8, 5, 2],
                    backgroundColor: '#1e3a5f'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Attendance Trend Chart
    const attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx) {
        new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
                datasets: [{
                    label: 'Attendance %',
                    data: [95, 92, 88, 85, 90, 87],
                    borderColor: '#1e3a5f',
                    backgroundColor: 'rgba(30, 58, 95, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 70,
                        max: 100
                    }
                }
            }
        });
    }
}

// Risk Calculation
function initRiskCalculation() {
    const calculateBtn = document.getElementById('calculateRisk');
    if (calculateBtn) {
        calculateBtn.addEventListener('click', async () => {
            calculateBtn.disabled = true;
            calculateBtn.innerHTML = '<span class="spinner"></span> Calculating...';
            
            try {
                const response = await fetch('/opencode-php/risk/calculate', {
                    method: 'POST'
                });
                const data = await response.json();
                
                if (data.success) {
                    showMessage(calculateBtn, 'Risk calculations updated successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage(calculateBtn, 'Error: ' + data.message, 'error');
                }
            } catch (error) {
                showMessage(calculateBtn, 'An error occurred', 'error');
            }
            
            calculateBtn.disabled = false;
            calculateBtn.textContent = 'Calculate Risk';
        });
    }
}

// File Upload
function initFileUpload() {
    const fileInputs = document.querySelectorAll('.file-upload input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const uploadArea = input.closest('.file-upload');
                const fileName = uploadArea.querySelector('.file-name');
                if (fileName) {
                    fileName.textContent = file.name;
                }
            }
        });
    });
    
    // Drag and drop
    document.querySelectorAll('.file-upload').forEach(area => {
        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('dragover');
        });
        
        area.addEventListener('dragleave', () => {
            area.classList.remove('dragover');
        });
        
        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
            
            const input = area.querySelector('input[type="file"]');
            if (input && e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                const event = new Event('change');
                input.dispatchEvent(event);
            }
        });
    });
}

// Notifications
function initNotifications() {
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationPanel = document.querySelector('.notification-panel');
    
    if (notificationBtn && notificationPanel) {
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationPanel.classList.toggle('active');
        });
        
        document.addEventListener('click', () => {
            notificationPanel.classList.remove('active');
        });
    }
    
    // Mark as read
    document.querySelectorAll('.mark-read').forEach(btn => {
        btn.addEventListener('click', async () => {
            const alertId = btn.dataset.id;
            await fetch(`/opencode-php/alerts/mark-read?id=${alertId}`);
            btn.closest('.alert-item')?.remove();
        });
    });
}

// Helper Functions
function showMessage(element, message, type = 'info') {
    const existingMsg = element.parentElement.querySelector('.message');
    if (existingMsg) existingMsg.remove();
    
    const msgDiv = document.createElement('div');
    msgDiv.className = `message ${type}`;
    msgDiv.textContent = message;
    
    element.parentElement.insertBefore(msgDiv, element);
    
    setTimeout(() => msgDiv.remove(), 5000);
}

function confirmAction(message) {
    return confirm(message || 'Are you sure you want to perform this action?');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Grade Entry
function initGradeEntry() {
    const gradeInputs = document.querySelectorAll('.grade-input');
    
    gradeInputs.forEach(input => {
        input.addEventListener('change', () => {
            const maxScore = parseFloat(input.dataset.max) || 100;
            const score = parseFloat(input.value);
            
            if (score > maxScore) {
                input.value = maxScore;
            }
            
            updateGradePreview(input);
        });
    });
}

// Attendance Taking
function initAttendanceTaking() {
    const presentBtn = document.querySelectorAll('.attendance-present');
    const absentBtn = document.querySelectorAll('.attendance-absent');
    const lateBtn = document.querySelectorAll('.attendance-late');
    
    const updateAttendance = (checkbox, status) => {
        const row = checkbox.closest('tr');
        const statusCell = row.querySelector('.status-cell');
        
        if (checkbox.checked) {
            statusCell.textContent = status;
            statusCell.className = `status-cell attendance-cell ${status}`;
        }
    };
    
    document.querySelectorAll('.attendance-checkbox').forEach(cb => {
        cb.addEventListener('change', () => {
            const row = cb.closest('tr');
            const status = row.dataset.status || 'present';
            updateAttendance(cb, status);
        });
    });
}

// Export Functions
async function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = [];
    table.querySelectorAll('tr').forEach(row => {
        const cells = [];
        row.querySelectorAll('th, td').forEach(cell => {
            cells.push(cell.textContent.trim());
        });
        rows.push(cells.join('\t'));
    });
    
    const blob = new Blob([rows.join('\n')], { type: 'application/vnd.ms-excel' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename + '.xls';
    a.click();
    URL.revokeObjectURL(url);
}

// Search functionality
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

// AJAX helpers
async function apiRequest(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, message: error.message };
    }
}
