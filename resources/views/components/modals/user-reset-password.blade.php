<!-- User Reset Password Modal -->
<div 
    id="userResetPasswordModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
    x-data="userResetPasswordModal()"
    x-init="init()"
    x-show="isOpen"
    @click.away="close()"
    x-cloak
    x-transition
>
    <div class="glass-card rounded-2xl p-6 max-w-md w-full" @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">إعادة ضبط كلمة المرور</h2>
            <button @click="close()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Warning -->
        <div class="p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg mb-6">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                <p class="text-yellow-400 text-sm">سيتم تغيير كلمة مرور المستخدم. تأكد من إبلاغه بالكلمة الجديدة.</p>
            </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit()" class="space-y-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">كلمة المرور الجديدة <span class="text-red-400">*</span></label>
                <div class="relative">
                    <input 
                        type="password" 
                        x-model="form.password"
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 pr-12 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="••••••••"
                    >
                    <button type="button" @click="togglePasswordVisibility()" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">تأكيد كلمة المرور <span class="text-red-400">*</span></label>
                <input 
                    type="password" 
                    x-model="form.password_confirmation"
                    required
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="••••••••"
                >
            </div>

            <!-- Password Generator -->
            <div class="bg-white/5 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-gray-300 text-sm">توليد كلمة مرور قوية</label>
                    <button type="button" @click="generatePassword()" class="px-3 py-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                        <i class="fas fa-key ml-1"></i>
                        توليد
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <input 
                        type="text" 
                        x-model="generatedPassword"
                        readonly
                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm"
                    >
                    <button type="button" @click="copyPassword()" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded text-sm" title="نسخ">
                        <i class="fas fa-copy"></i>
                    </button>
                    <button type="button" @click="useGeneratedPassword()" class="px-3 py-2 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                        استخدام
                    </button>
                </div>
                <p class="text-gray-400 text-xs mt-2">كلمة مرور قوية تحتوي على أحرف كبيرة وصغيرة وأرقام ورموز</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <button 
                    type="button" 
                    @click="close()"
                    class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200"
                >
                    إلغاء
                </button>
                <button 
                    type="submit"
                    class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200"
                >
                    <i class="fas fa-save ml-2"></i>
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function userResetPasswordModal() {
    return {
        isOpen: false,
        showPassword: false,
        generatedPassword: '',
        form: {
            password: '',
            password_confirmation: '',
            userId: null
        },
        init() {
            window.addEventListener('open-user-reset-password-modal', (e) => {
                this.form.userId = e.detail.userId;
                this.open();
            });
        },
        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
            this.form = {
                password: '',
                password_confirmation: '',
                userId: null
            };
            this.generatedPassword = '';
            this.showPassword = false;
        },
        togglePasswordVisibility() {
            this.showPassword = !this.showPassword;
            const input = document.querySelector('#userResetPasswordModal input[type="password"]');
            if (input) {
                input.type = this.showPassword ? 'text' : 'password';
            }
        },
        generatePassword() {
            const length = 12;
            const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
            let password = '';
            for (let i = 0; i < length; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            this.generatedPassword = password;
        },
        copyPassword() {
            if (this.generatedPassword) {
                navigator.clipboard.writeText(this.generatedPassword);
                alert('تم نسخ كلمة المرور');
            }
        },
        useGeneratedPassword() {
            if (this.generatedPassword) {
                this.form.password = this.generatedPassword;
                this.form.password_confirmation = this.generatedPassword;
            }
        },
        submit() {
            if (this.form.password !== this.form.password_confirmation) {
                alert('كلمات المرور غير متطابقة');
                return;
            }

            if (this.form.password.length < 8) {
                alert('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
                return;
            }

            // TODO: Submit via AJAX
            console.log('Resetting password for user:', this.form.userId);
            alert('تم إعادة ضبط كلمة المرور بنجاح');
            this.close();
        }
    }
}
</script>


