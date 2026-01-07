<!-- Payment Modal -->
<div 
    id="paymentModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
    x-data="paymentModal()"
    x-init="init()"
    x-show="isOpen"
    @click.away="close()"
    x-cloak
    x-transition
>
    <div class="glass-card rounded-2xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">إضافة دفعة جديدة</h2>
            <button @click="close()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form method="POST" enctype="multipart/form-data" @submit.prevent="submit()" class="space-y-6" id="paymentForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">رقم الدفعة</label>
                    <input 
                        type="text" 
                        name="payment_no"
                        x-model="form.paymentNumber"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="PAY-001"
                    >
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">المبلغ <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input 
                            type="number" 
                            name="amount"
                            x-model="form.amount"
                            required
                            step="0.01"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 pr-16 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            placeholder="0.00"
                        >
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">ر.س</span>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">تاريخ الدفعة <span class="text-red-400">*</span></label>
                    <input 
                        type="date" 
                        name="paid_at"
                        x-model="form.date"
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">طريقة الدفع</label>
                    <select 
                        name="method"
                        x-model="form.paymentMethod"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                        <option value="cash">نقدي</option>
                        <option value="transfer">تحويل بنكي</option>
                        <option value="check">شيك</option>
                        <option value="electronic">إلكتروني</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">الحالة</label>
                    <select 
                        name="status"
                        x-model="form.status"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                        <option value="paid">مدفوع</option>
                        <option value="pending">قيد الانتظار</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">مرفق (اختياري)</label>
                    <input 
                        type="file" 
                        name="attachment"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                </div>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">الملاحظات</label>
                <textarea 
                    name="notes"
                    x-model="form.notes"
                    rows="3"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="أي ملاحظات إضافية حول الدفعة..."
                ></textarea>
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
function paymentModal() {
    return {
        isOpen: false,
        form: {
            paymentNumber: '',
            amount: '',
            date: new Date().toISOString().split('T')[0],
            paymentMethod: 'transfer',
            status: 'paid',
            referenceNumber: '',
            notes: '',
            invoiceId: null
        },
        init() {
            // Listen for open event
            window.addEventListener('open-payment-modal', (e) => {
                this.open(e.detail.invoiceId);
            });
        },
        open(invoiceId = null) {
            this.isOpen = true;
            this.form.invoiceId = invoiceId;
            // Auto-generate payment number
            if (!this.form.paymentNumber) {
                const year = new Date().getFullYear();
                const random = String(Math.floor(Math.random() * 10000)).padStart(4, '0');
                this.form.paymentNumber = 'PAY-' + year + '-' + random;
            }
            // Set today's date
            this.form.date = new Date().toISOString().split('T')[0];
        },
        close() {
            this.isOpen = false;
            // Reset form
            this.form = {
                paymentNumber: '',
                amount: '',
                date: new Date().toISOString().split('T')[0],
                paymentMethod: 'transfer',
                status: 'paid',
                referenceNumber: '',
                notes: '',
                invoiceId: null
            };
        },
        submit() {
            const form = document.getElementById('paymentForm');
            const formData = new FormData(form);
            const invoiceId = this.form.invoiceId;
            
            // Set form action
            const actionUrl = `/financials/${invoiceId}/payments`;
            
            // Submit form
            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json().catch(() => ({ success: true }));
                }
            })
            .then(data => {
                if (data && data.success !== false) {
                    window.location.reload();
                } else {
                    alert(data?.message || 'حدث خطأ أثناء إضافة الدفعة');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Try to reload anyway
                window.location.reload();
            });
        }
    }
}

// Global function to open modal (for non-Alpine contexts)
window.openPaymentModal = function(invoiceId) {
    window.dispatchEvent(new CustomEvent('open-payment-modal', { detail: { invoiceId } }));
}
</script>

