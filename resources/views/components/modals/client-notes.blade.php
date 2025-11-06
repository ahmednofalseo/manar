<!-- Client Notes Modal -->
<div 
    id="clientNotesModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
    x-data="clientNotesModal()"
    x-init="init()"
    x-show="isOpen"
    @click.away="close()"
    x-cloak
    x-transition
>
    <div class="glass-card rounded-2xl p-6 max-w-2xl w-full" @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">ملاحظات العميل</h2>
            <button @click="close()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit()" class="space-y-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">الملاحظة <span class="text-red-400">*</span></label>
                <textarea 
                    x-model="form.note"
                    required
                    rows="5"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="اكتب ملاحظة حول العميل..."
                ></textarea>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">الحالة</label>
                <select 
                    x-model="form.status"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                    <option value="pending">قيد المراجعة</option>
                    <option value="resolved">محلولة</option>
                    <option value="important">مهمة</option>
                </select>
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
function clientNotesModal() {
    return {
        isOpen: false,
        form: {
            note: '',
            status: 'pending',
            clientId: null
        },
        init() {
            window.addEventListener('open-client-notes-modal', (e) => {
                this.form.clientId = e.detail.clientId;
                this.open();
            });
        },
        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
            this.form = {
                note: '',
                status: 'pending',
                clientId: null
            };
        },
        submit() {
            // TODO: Submit via AJAX
            console.log('Submitting note for client:', this.form);
            alert('تم إضافة الملاحظة بنجاح');
            this.close();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }
}
</script>


