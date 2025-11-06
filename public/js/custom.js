// Toggle Password Visibility
function togglePasswordVisibility(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Show Toast Notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${
        type === 'success' 
            ? 'bg-green-500 text-white' 
            : 'bg-red-500 text-white'
    }`;
    toast.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// Auto-hide toasts after page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide existing toasts after 5 seconds
    const existingToasts = document.querySelectorAll('.toast-auto-hide');
    existingToasts.forEach(toast => {
        setTimeout(() => {
            toast.style.transition = 'opacity 0.3s ease-out';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    });
    
    // Initialize password toggle buttons
    const passwordToggleButtons = document.querySelectorAll('[data-toggle-password]');
    passwordToggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const inputId = this.getAttribute('data-input-id');
            const iconId = this.getAttribute('data-icon-id');
            togglePasswordVisibility(inputId, iconId);
        });
    });
});

// Language Toggle (Placeholder)
function toggleLanguage() {
    // Implementation for language switching
    console.log('Language toggle clicked');
}

// User Dropdown Toggle
function toggleUserDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const button = event.target.closest('[onclick*="toggleUserDropdown"]');
    
    if (dropdown && !dropdown.contains(event.target) && !button) {
        dropdown.classList.add('hidden');
    }
});

// Mobile Sidebar Toggle
function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobileOverlay');
    
    if (sidebar && overlay) {
        sidebar.classList.toggle('translate-x-full');
        overlay.classList.toggle('hidden');
        
        // Prevent body scroll when sidebar is open
        if (!sidebar.classList.contains('translate-x-full')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
}

// Mobile Search Toggle
function toggleMobileSearch() {
    // Implementation for mobile search modal
    showToast('سيتم تنفيذ البحث المتنقل قريباً', 'info');
}

// Close mobile sidebar when clicking on a link
function closeMobileSidebarOnClick() {
    if (window.innerWidth < 1024) {
        toggleMobileSidebar();
    }
}

// Close mobile sidebar on window resize (if larger than lg)
window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');
        
        if (sidebar && overlay) {
            sidebar.classList.remove('translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }
});
