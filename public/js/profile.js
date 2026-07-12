// Profile AJAX Handler - Best Practice Implementation
class ProfileHandler {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        // Wait for DOM to be fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.attachListeners());
        } else {
            this.attachListeners();
        }
    }

    attachListeners() {
        // Account info form submission
        const accountForm = document.getElementById('account-info-form');
        if (accountForm) {
            accountForm.addEventListener('submit', (e) => this.handleAccountInfoSubmit(e));
        }

        // Password form submission
        const passwordForm = document.getElementById('password-form');
        if (passwordForm) {
            passwordForm.addEventListener('submit', (e) => this.handlePasswordSubmit(e));
        }

        // Real-time password strength
        const passwordInput = document.querySelector('#password-form input[name="password"]');
        if (passwordInput) {
            passwordInput.addEventListener('input', (e) => this.checkPasswordStrength(e.target.value));
        }
    }

    async handleAccountInfoSubmit(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="w-5 h-5 animate-spin inline mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `;

        const formData = new FormData(form);
        
        try {
            const response = await fetch(form.action, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    full_name: formData.get('full_name'),
                    email: formData.get('email'),
                    phone_number: formData.get('phone_number'),
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Success - stay on profile page
                this.showToast('success', result.message || 'Informasi berhasil diperbarui');
                
                // Update form values from response
                if (result.user) {
                    const nameInput = form.querySelector('input[name="full_name"]');
                    const phoneInput = form.querySelector('input[name="phone_number"]');
                    if (nameInput && result.user.full_name) nameInput.value = result.user.full_name;
                    if (phoneInput && result.user.phone_number) phoneInput.value = result.user.phone_number;
                }
            } else {
                // Error handling
                this.showToast('error', result.message || 'Terjadi kesalahan');
                
                // Clear previous errors
                form.querySelectorAll('.error-message').forEach(el => el.remove());
                form.querySelectorAll('input').forEach(inp => inp.classList.remove('border-red-500'));
                
                if (result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('border', 'border-red-500');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'text-red-500 text-xs mt-1 ml-1 error-message';
                            errorDiv.textContent = result.errors[field][0];
                            input.parentNode.appendChild(errorDiv);
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Request failed:', error);
            this.showToast('error', 'Koneksi bermasalah. Silakan coba lagi.');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }

        return false;
    }

    async handlePasswordSubmit(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="w-5 h-5 animate-spin inline mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `;

        const formData = new FormData(form);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    current_password: formData.get('current_password'),
                    password: formData.get('password'),
                    password_confirmation: formData.get('password_confirmation'),
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                this.showToast('success', result.message || 'Sandi berhasil diperbarui');
                form.reset(); // Clear form after success
                
                // Clear password strength indicator
                const strengthBar = document.getElementById('password-strength-bar');
                const strengthText = document.getElementById('password-strength-text');
                if (strengthBar) strengthBar.style.width = '0%';
                if (strengthText) strengthText.textContent = '';
            } else {
                this.showToast('error', result.message || 'Terjadi kesalahan');
                
                // Clear previous errors
                form.querySelectorAll('.error-message').forEach(el => el.remove());
                form.querySelectorAll('input').forEach(inp => inp.classList.remove('border-red-500'));
                
                if (result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('border', 'border-red-500');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'text-red-500 text-xs mt-1 ml-1 error-message';
                            errorDiv.textContent = result.errors[field][0];
                            input.parentNode.appendChild(errorDiv);
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Request failed:', error);
            this.showToast('error', 'Koneksi bermasalah. Silakan coba lagi.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }

        return false;
    }

    async checkPasswordStrength(password) {
        if (!password || password.length < 3) return;

        try {
            const response = await fetch(`/profile/password-strength?password=${encodeURIComponent(password)}`);
            const data = await response.json();
            
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');
            
            if (strengthBar && strengthText) {
                strengthBar.style.width = `${data.percentage}%`;
                
                const colors = {
                    0: 'bg-red-500',
                    20: 'bg-red-500',
                    40: 'bg-orange-500',
                    60: 'bg-yellow-500',
                    80: 'bg-green-400',
                    100: 'bg-green-600'
                };
                
                const colorClass = Object.entries(colors).reduce((prev, [threshold, color]) => {
                    return data.percentage >= parseInt(threshold) ? color : prev;
                }, 'bg-red-500');
                
                strengthBar.className = `h-2 rounded-full transition-all duration-300 ${colorClass}`;
                strengthText.textContent = `Kekuatan: ${data.level}`;
                strengthText.className = `text-xs font-bold mt-1 ${colorClass.replace('bg-', 'text-')}`;
            }
        } catch (error) {
            console.error('Password strength check failed:', error);
        }
    }

    showToast(type, message) {
        // Remove existing toasts
        document.querySelectorAll('.profile-toast').forEach(t => t.remove());

        const toast = document.createElement('div');
        toast.className = `profile-toast fixed top-4 right-4 z-[9999] px-6 py-4 rounded-2xl shadow-[6px_6px_12px_#a3b1c6,-6px_-6px_12px_#ffffff] border border-white/20 font-bold transition-all duration-500 ${
            type === 'success' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200'
        }`;
        toast.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                        : '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                    }
                </svg>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('opacity-100'), 10);

        // Auto-remove after 4 seconds
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }
}

// Initialize - single instance
if (typeof window.profileHandler === 'undefined') {
    window.profileHandler = new ProfileHandler();
}
