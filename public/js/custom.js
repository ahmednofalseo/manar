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

// Language Toggle
function toggleLanguage() {
    const currentLocale = document.documentElement.lang || 'ar';
    const newLocale = currentLocale === 'ar' ? 'en' : 'ar';
    window.location.href = `/language/${newLocale}`;
}

// Notifications Dropdown (Alpine.js component)
function notificationsDropdown() {
    return {
        isOpen: false,
        notifications: [],
        unreadCount: 0,
        taskSound: null,
        commentSound: null,
        isLoading: false,
        lastNotificationCheck: null,
        
        init() {
            // Load notification sounds (using Web Audio API for better control)
            this.audioEnabled = false; // Will be enabled after user interaction
            
            try {
                // Create audio context for sounds (will be resumed on user interaction)
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                
                // Task notification sound (higher pitch, shorter)
                this.taskSound = this.createTaskSound();
                
                // Comment notification sound (lower pitch, longer)
                this.commentSound = this.createCommentSound();
                
                // Enable audio on first user interaction (required by browsers)
                const enableAudio = () => {
                    if (this.audioContext && this.audioContext.state === 'suspended') {
                        this.audioContext.resume().then(() => {
                            this.audioEnabled = true;
                            console.log('Audio enabled');
                        }).catch(e => {
                            console.warn('Could not enable audio:', e);
                        });
                    } else {
                        this.audioEnabled = true;
                    }
                    // Remove listeners after first interaction
                    document.removeEventListener('click', enableAudio);
                    document.removeEventListener('touchstart', enableAudio);
                    document.removeEventListener('keydown', enableAudio);
                };
                
                // Listen for user interaction to enable audio
                document.addEventListener('click', enableAudio, { once: true });
                document.addEventListener('touchstart', enableAudio, { once: true });
                document.addEventListener('keydown', enableAudio, { once: true });
            } catch (e) {
                console.warn('Audio context not supported, using fallback');
                // Fallback: try to load audio files if they exist
                this.taskSound = new Audio();
                this.commentSound = new Audio();
                this.audioEnabled = true; // Enable for fallback
            }
            
            // Load notifications
            this.loadNotifications();
            
            // Poll for new notifications every 30 seconds
            setInterval(() => {
                this.loadNotifications(true);
            }, 30000);
        },
        
        createTaskSound() {
            // Create a short, high-pitched beep for task notifications
            const audioContext = this.audioContext;
            return {
                play: async () => {
                    try {
                        if (!audioContext) {
                            console.warn('Audio context not available');
                            return;
                        }
                        
                        // Resume audio context if suspended (required by browsers)
                        if (audioContext.state === 'suspended') {
                            await audioContext.resume();
                        }
                        
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();
                        
                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);
                        
                        oscillator.frequency.value = 800; // Higher pitch
                        oscillator.type = 'sine';
                        
                        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                        
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.3);
                    } catch (e) {
                        console.warn('Could not play task sound:', e);
                    }
                }
            };
        },
        
        createCommentSound() {
            // Create a longer, lower-pitched beep for comment notifications
            const audioContext = this.audioContext;
            return {
                play: async () => {
                    try {
                        if (!audioContext) {
                            console.warn('Audio context not available');
                            return;
                        }
                        
                        // Resume audio context if suspended (required by browsers)
                        if (audioContext.state === 'suspended') {
                            await audioContext.resume();
                        }
                        
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();
                        
                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);
                        
                        oscillator.frequency.value = 600; // Lower pitch
                        oscillator.type = 'sine';
                        
                        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                        
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.5);
                    } catch (e) {
                        console.warn('Could not play comment sound:', e);
                    }
                }
            };
        },
        
        async loadNotifications(silent = false) {
            // Prevent multiple simultaneous requests
            if (this.isLoading) {
                return;
            }
            
            this.isLoading = true;
            
            try {
                const response = await fetch('/notifications', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                const data = await response.json();
                const oldUnreadCount = this.unreadCount;
                const oldNotificationIds = new Set(this.notifications.map(n => n.id));
                
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
                
                // Play sound if new notifications arrived (only once, not for each notification)
                if (!silent && this.unreadCount > oldUnreadCount && this.audioEnabled) {
                    // Find truly new notifications (not in the old list)
                    const newNotifications = this.notifications.filter(n => 
                        !n.read && 
                        !oldNotificationIds.has(n.id) &&
                        new Date(n.created_at) > new Date(Date.now() - 60000) // Last 60 seconds
                    );
                    
                    if (newNotifications.length > 0) {
                        // Play sound only once, not for each notification
                        // Determine which sound to play based on the first new notification
                        const firstNewNotification = newNotifications[0];
                        
                        try {
                            // Resume audio context if suspended
                            if (this.audioContext && this.audioContext.state === 'suspended') {
                                await this.audioContext.resume();
                            }
                            
                            if (firstNewNotification.type === 'task_assigned' && this.taskSound) {
                                try {
                                    this.taskSound.play();
                                } catch (e) {
                                    console.warn('Could not play task sound:', e);
                                }
                            } else if (firstNewNotification.type === 'task_comment' && this.commentSound) {
                                try {
                                    // Ensure audio context is resumed
                                    if (this.audioContext && this.audioContext.state === 'suspended') {
                                        await this.audioContext.resume();
                                    }
                                    this.commentSound.play();
                                } catch (e) {
                                    console.warn('Could not play comment sound:', e);
                                }
                            }
                        } catch (e) {
                            console.warn('Could not play notification sound:', e);
                        }
                    }
                }
                
                this.lastNotificationCheck = Date.now();
            } catch (error) {
                console.error('Error loading notifications:', error);
            } finally {
                this.isLoading = false;
            }
        },
        
        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadNotifications();
            }
        },
        
        close() {
            this.isOpen = false;
        },
        
        async openNotification(notification) {
            // Mark as read
            if (!notification.read) {
                await this.markAsRead(notification.id);
                notification.read = true;
                if (this.unreadCount > 0) {
                    this.unreadCount--;
                }
            }
            
            // Navigate to task
            if (notification.data && notification.data.task_id) {
                window.location.href = `/tasks/${notification.data.task_id}`;
            }
        },
        
        async markAsRead(id) {
            try {
                await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },
        
        async markAllAsRead() {
            try {
                await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                this.notifications.forEach(n => {
                    n.read = true;
                });
                this.unreadCount = 0;
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },
        
        async deleteNotification(id) {
            const locale = document.documentElement.lang || 'ar';
            const message = locale === 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?';
            if (!confirm(message)) {
                return;
            }
            
            try {
                await fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                this.notifications = this.notifications.filter(n => n.id !== id);
                if (this.unreadCount > 0) {
                    this.unreadCount--;
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        },
        
        async deleteAll() {
            const locale = document.documentElement.lang || 'ar';
            const message = locale === 'ar' ? 'هل أنت متأكد من حذف جميع الإشعارات؟' : 'Are you sure you want to delete all notifications?';
            if (!confirm(message)) {
                return;
            }
            
            try {
                await fetch('/notifications', {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                this.notifications = [];
                this.unreadCount = 0;
            } catch (error) {
                console.error('Error deleting all notifications:', error);
            }
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);
            const locale = document.documentElement.lang || 'ar';
            
            if (minutes < 1) {
                return locale === 'ar' ? 'الآن' : 'Just now';
            } else if (minutes < 60) {
                return locale === 'ar' ? `${minutes} دقيقة` : `${minutes} minutes ago`;
            } else if (hours < 24) {
                return locale === 'ar' ? `${hours} ساعة` : `${hours} hours ago`;
            } else if (days < 7) {
                return locale === 'ar' ? `${days} يوم` : `${days} days ago`;
            } else {
                return date.toLocaleDateString(locale);
            }
        }
    };
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
