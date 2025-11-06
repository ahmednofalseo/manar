<div class="space-y-4 md:space-y-6">
    <!-- Financial Summary -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6">
            <div>
                <p class="text-gray-400 text-sm mb-2">قيمة المشروع</p>
                <p class="text-2xl font-bold text-white">150,000 <span class="text-lg text-gray-400">ر.س</span></p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-2">المحصل</p>
                <p class="text-2xl font-bold text-green-400">75,000 <span class="text-lg text-gray-400">ر.س</span></p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-2">المتبقي</p>
                <p class="text-2xl font-bold text-red-400">75,000 <span class="text-lg text-gray-400">ر.س</span></p>
            </div>
        </div>
        <div class="w-full bg-white/5 rounded-full h-3">
            <div class="bg-primary-400 h-3 rounded-full" style="width: 50%"></div>
        </div>
    </div>

    <!-- Payments Timeline -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">المخطط الزمني للدفعات</h2>
            <button onclick="openInvoiceModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
                <i class="fas fa-plus ml-2"></i>
                إنشاء فاتورة PDF
            </button>
        </div>

        <!-- Payments Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-right border-b border-white/10">
                        <th class="text-gray-400 text-sm font-normal pb-3">رقم الدفعة</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">التاريخ</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">المبلغ</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">الحالة</th>
                        <th class="text-gray-400 text-sm font-normal pb-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="py-3 text-white text-sm">#PAY-001</td>
                        <td class="py-3 text-gray-300 text-sm">2025-01-20</td>
                        <td class="py-3 text-white font-semibold">50,000 ر.س</td>
                        <td class="py-3"><span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">مدفوع</span></td>
                        <td class="py-3">
                            <button class="text-primary-400 hover:text-primary-300"><i class="fas fa-file-pdf"></i></button>
                        </td>
                    </tr>
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="py-3 text-white text-sm">#PAY-002</td>
                        <td class="py-3 text-gray-300 text-sm">2025-02-20</td>
                        <td class="py-3 text-white font-semibold">25,000 ر.س</td>
                        <td class="py-3"><span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs">جزئي</span></td>
                        <td class="py-3">
                            <button class="text-primary-400 hover:text-primary-300"><i class="fas fa-file-pdf"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function openInvoiceModal() {
    alert('سيتم فتح نافذة إنشاء فاتورة PDF');
}
</script>
