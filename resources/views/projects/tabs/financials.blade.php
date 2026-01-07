<div class="space-y-4 md:space-y-6">
    <!-- Financial Summary -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6">
            <div>
                <p class="text-gray-400 text-sm mb-2">قيمة المشروع</p>
                <p class="text-2xl font-bold text-white">{{ number_format($project->value, 2) }} <span class="text-lg text-gray-400">ر.س</span></p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-2">المحصل</p>
                <p class="text-2xl font-bold text-green-400">0 <span class="text-lg text-gray-400">ر.س</span></p>
                <p class="text-gray-400 text-xs mt-1">سيتم ربطه بوحدة الفواتير</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-2">المتبقي</p>
                <p class="text-2xl font-bold text-red-400">{{ number_format($project->value, 2) }} <span class="text-lg text-gray-400">ر.س</span></p>
            </div>
        </div>
        <div class="w-full bg-white/5 rounded-full h-3">
            <div class="bg-primary-400 h-3 rounded-full" style="width: 0%"></div>
        </div>
    </div>

    <!-- Payments Timeline -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">المخطط الزمني للدفعات</h2>
            <a href="{{ route('financials.index') }}?project_id={{ $project->id }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
                <i class="fas fa-file-invoice-dollar ml-2"></i>
                إدارة الفواتير
            </a>
        </div>

        <!-- Payments Table -->
        <div class="text-center py-12">
            <i class="fas fa-file-invoice-dollar text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-bold text-white mb-2">لا توجد فواتير</h3>
            <p class="text-gray-400 mb-4">قم بإنشاء فواتير للمشروع من صفحة إدارة الفواتير</p>
            <a href="{{ route('financials.create', ['project_id' => $project->id]) }}" class="inline-block px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-plus ml-2"></i>
                إنشاء فاتورة
            </a>
        </div>
    </div>
</div>
