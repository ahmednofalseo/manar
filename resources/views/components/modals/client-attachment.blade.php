<!-- Client Attachment Modal -->
<div 
    id="clientAttachmentModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
    x-data="clientAttachmentModal()"
    x-init="init()"
    x-show="isOpen"
    @click.away="close()"
    x-cloak
    x-transition
>
    <div class="glass-card rounded-2xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">مرفقات العميل</h2>
            <button @click="close()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Upload Form -->
        <form @submit.prevent="submit()" class="space-y-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">رفع الملفات</label>
                <div class="flex items-center gap-4">
                    <input 
                        type="file" 
                        name="attachments[]"
                        multiple
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="hidden"
                        id="attachmentsInput"
                        @change="handleFilesSelect($event)"
                    >
                    <label for="attachmentsInput" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer transition-all duration-200">
                        <i class="fas fa-upload ml-2"></i>
                        اختر الملفات
                    </label>
                    <span x-show="selectedFiles.length > 0" class="text-gray-300 text-sm" x-text="selectedFiles.length + ' ملف محدد'"></span>
                </div>
                <p class="text-gray-400 text-xs mt-1">يمكن رفع عدة ملفات (PDF, JPG, PNG)</p>
            </div>

            <!-- Selected Files List -->
            <div x-show="selectedFiles.length > 0" class="space-y-2">
                <template x-for="(file, index) in selectedFiles" :key="index">
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file text-primary-400"></i>
                            <span class="text-white text-sm" x-text="file.name"></span>
                            <span class="text-gray-400 text-xs" x-text="formatFileSize(file.size)"></span>
                        </div>
                        <button type="button" @click="removeFile(index)" class="text-red-400 hover:text-red-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </template>
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
                    :disabled="loading"
                    class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'" class="ml-2"></i>
                    <span x-text="loading ? 'جاري الرفع...' : 'حفظ'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function clientAttachmentModal() {
    return {
        isOpen: false,
        clientId: null,
        selectedFiles: [],
        init() {
            window.addEventListener('open-client-attachment-modal', (e) => {
                this.clientId = e.detail.clientId;
                this.open();
            });
        },
        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
            this.selectedFiles = [];
            this.clientId = null;
        },
        handleFilesSelect(event) {
            const files = Array.from(event.target.files);
            this.selectedFiles = files.map(file => ({
                name: file.name,
                size: file.size
            }));
        },
        removeFile(index) {
            this.selectedFiles.splice(index, 1);
            const input = document.getElementById('attachmentsInput');
            if (input) {
                input.value = '';
            }
        },
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },
        loading: false,
        errors: {},
        async submit() {
            if (this.selectedFiles.length === 0) {
                this.showToast('error', 'يرجى اختيار ملف واحد على الأقل');
                return;
            }

            this.loading = true;
            this.errors = {};

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const formData = new FormData();
                
                // إضافة الملفات
                const fileInput = document.getElementById('attachmentsInput');
                if (fileInput && fileInput.files) {
                    for (let i = 0; i < fileInput.files.length; i++) {
                        formData.append('attachments[]', fileInput.files[i]);
                    }
                }
                
                formData.append('_token', csrfToken);

                const response = await fetch(`/clients/${this.clientId}/attachments`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success !== false) {
                    this.close();
                    this.showToast('success', result.message || 'تم رفع الملفات بنجاح');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    if (result.errors) {
                        this.errors = result.errors;
                    } else {
                        this.showToast('error', result.message || 'حدث خطأ أثناء رفع الملفات');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                this.showToast('error', 'حدث خطأ أثناء رفع الملفات');
            } finally {
                this.loading = false;
            }
        },
        showToast(type, message) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md text-white animate-slide-in ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            toast.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                        <span>${message}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }
    }
}
</script>


