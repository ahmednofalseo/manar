<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-bold text-white">المهام</h2>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg text-sm">
                <i class="fas fa-file-import ml-2"></i>
                استيراد من قالب
            </button>
            <button onclick="openTaskModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
                <i class="fas fa-plus ml-2"></i>
                إنشاء مهمة
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <select class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm">
            <option>جميع المكلّفين</option>
        </select>
        <select class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm">
            <option>جميع الحالات</option>
        </select>
        <input type="date" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white text-sm">
    </div>

    <!-- Tasks Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3">المهمة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">المكلّف</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">تاريخ الاستحقاق</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الحالة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-white/5 hover:bg-white/5">
                    <td class="py-3 text-white text-sm">مراجعة المخططات المعمارية</td>
                    <td class="py-3 text-gray-300 text-sm">محمد أحمد</td>
                    <td class="py-3 text-gray-300 text-sm">2025-11-10</td>
                    <td class="py-3"><span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs">قيد التنفيذ</span></td>
                    <td class="py-3">
                        <button class="text-primary-400 hover:text-primary-300"><i class="fas fa-edit"></i></button>
                    </td>
                </tr>
                <tr class="border-b border-white/5 hover:bg-white/5">
                    <td class="py-3 text-white text-sm">إعداد تقرير التقدم</td>
                    <td class="py-3 text-gray-300 text-sm">فاطمة سالم</td>
                    <td class="py-3 text-gray-300 text-sm">2025-11-08</td>
                    <td class="py-3"><span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs">جديد</span></td>
                    <td class="py-3">
                        <button class="text-primary-400 hover:text-primary-300"><i class="fas fa-edit"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function openTaskModal() {
    alert('سيتم فتح نافذة إنشاء مهمة');
}
</script>
