<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">الطرف الثالث ({{ $project->thirdParties->count() }})</h2>
        @can('update', $project)
        <button onclick="openThirdPartyModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
            <i class="fas fa-plus ml-2"></i>
            إضافة
        </button>
        @endcan
    </div>

    @if($project->thirdParties->count() > 0)
    <!-- Third Party Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3">الاسم/الجهة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">التاريخ</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الملاحظة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->thirdParties as $thirdParty)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="py-3 text-white text-sm font-semibold">{{ $thirdParty->name }}</td>
                        <td class="py-3 text-gray-300 text-sm">
                            {{ $thirdParty->date ? $thirdParty->date->format('Y-m-d') : '-' }}
                        </td>
                        <td class="py-3 text-gray-300 text-sm">
                            {{ $thirdParty->notes ? \Illuminate\Support\Str::limit($thirdParty->notes, 50) : '-' }}
                        </td>
                        <td class="py-3">
                            @can('update', $project)
                            <form action="{{ route('projects.thirdparty.destroy', [$project->id, $thirdParty->id]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطرف الثالث؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-handshake text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-bold text-white mb-2">لا توجد أطراف ثالثة</h3>
        <p class="text-gray-400 mb-4">قم بإضافة أطراف ثالثة للمشروع</p>
    </div>
    @endif
</div>

<!-- Third Party Modal -->
<div id="thirdPartyModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center" onclick="closeThirdPartyModal(event)">
    <div class="glass-card rounded-xl md:rounded-2xl p-6 max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">إضافة طرف ثالث</h3>
            <button onclick="closeThirdPartyModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('projects.thirdparty.store', $project->id) }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">الاسم/الجهة <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40" placeholder="مثال: شركة الكهرباء">
                </div>
                
                <div>
                    <label class="block text-gray-300 text-sm mb-2">التاريخ</label>
                    <input type="date" name="date" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                </div>
                
                <div>
                    <label class="block text-gray-300 text-sm mb-2">الملاحظة</label>
                    <textarea name="notes" rows="3" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40" placeholder="ملاحظات حول الطرف الثالث"></textarea>
                </div>
            </div>
            
            <div class="flex items-center gap-3 mt-6">
                <button type="button" onclick="closeThirdPartyModal()" class="flex-1 px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                    <i class="fas fa-save ml-2"></i>
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openThirdPartyModal() {
    document.getElementById('thirdPartyModal').classList.remove('hidden');
    document.getElementById('thirdPartyModal').classList.add('flex');
}

function closeThirdPartyModal(event) {
    if (!event || event.target === event.currentTarget || event.target.closest('button')) {
        document.getElementById('thirdPartyModal').classList.add('hidden');
        document.getElementById('thirdPartyModal').classList.remove('flex');
    }
}
</script>
