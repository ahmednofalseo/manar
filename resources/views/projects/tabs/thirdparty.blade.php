<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">الطرف الثالث</h2>
        <button onclick="openThirdPartyModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
            <i class="fas fa-plus ml-2"></i>
            إضافة
        </button>
    </div>

    <!-- Third Party Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3">الاسم/الجهة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">التاريخ</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الملاحظة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-white/5 hover:bg-white/5">
                    <td class="py-3 text-white text-sm">شركة الكهرباء</td>
                    <td class="py-3 text-gray-300 text-sm">2025-01-25</td>
                    <td class="py-3 text-gray-300 text-sm">توصيل كهرباء</td>
                    <td class="py-3">
                        <button class="text-primary-400 hover:text-primary-300 mr-2"><i class="fas fa-edit"></i></button>
                        <button class="text-red-400 hover:text-red-300"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <tr class="border-b border-white/5 hover:bg-white/5">
                    <td class="py-3 text-white text-sm">بلدية الرياض</td>
                    <td class="py-3 text-gray-300 text-sm">2025-02-10</td>
                    <td class="py-3 text-gray-300 text-sm">رخصة بناء</td>
                    <td class="py-3">
                        <button class="text-primary-400 hover:text-primary-300 mr-2"><i class="fas fa-edit"></i></button>
                        <button class="text-red-400 hover:text-red-300"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function openThirdPartyModal() {
    alert('سيتم فتح نافذة إضافة طرف ثالث');
}
</script>
