<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">المراحل</h2>
        <button onclick="openStageModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
            <i class="fas fa-plus ml-2"></i>
            تحديث مرحلة
        </button>
    </div>

    <!-- Timeline -->
    <div class="space-y-6">
        <!-- Stage 1 -->
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white"></i>
                </div>
                <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
            </div>
            <div class="flex-1 pb-6">
                <div class="bg-white/5 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-white font-semibold">معماري</h3>
                        <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">مكتمل</span>
                    </div>
                    <p class="text-gray-400 text-sm mb-2">تاريخ البدء: 2025-01-15</p>
                    <p class="text-gray-400 text-sm mb-3">تاريخ الانتهاء: 2025-02-28</p>
                    <div class="flex items-center gap-2">
                        <a href="#" class="text-primary-400 hover:text-primary-300 text-sm">
                            <i class="fas fa-file-pdf ml-1"></i>
                            عرض الملفات
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stage 2 -->
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-white"></i>
                </div>
                <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
            </div>
            <div class="flex-1 pb-6">
                <div class="bg-white/5 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-white font-semibold">إنشائي</h3>
                        <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs">جارٍ</span>
                    </div>
                    <p class="text-gray-400 text-sm mb-2">تاريخ البدء: 2025-03-01</p>
                    <p class="text-gray-400 text-sm mb-3">تاريخ الانتهاء المتوقع: 2025-04-15</p>
                    <div class="flex items-center gap-2">
                        <a href="#" class="text-primary-400 hover:text-primary-300 text-sm">
                            <i class="fas fa-file-pdf ml-1"></i>
                            عرض الملفات
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stage 3 -->
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-circle text-white text-xs"></i>
                </div>
                <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
            </div>
            <div class="flex-1 pb-6">
                <div class="bg-white/5 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-white font-semibold">كهربائي</h3>
                        <span class="bg-gray-500/20 text-gray-400 px-2 py-1 rounded text-xs">جديد</span>
                    </div>
                    <p class="text-gray-400 text-sm">لم يبدأ بعد</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openStageModal() {
    alert('سيتم فتح نافذة تحديث المرحلة');
}
</script>
