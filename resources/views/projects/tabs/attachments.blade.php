<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">المرفقات ({{ $project->attachments->count() }})</h2>
        @can('update', $project)
        <button onclick="openAttachmentModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
            <i class="fas fa-plus ml-2"></i>
            رفع ملف
        </button>
        @endcan
    </div>

    <!-- Upload Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <button onclick="openAttachmentModal('عقد')" class="bg-white/5 hover:bg-white/10 rounded-lg p-4 text-right border border-white/10 transition-all">
            <i class="fas fa-file-contract text-primary-400 text-2xl mb-2"></i>
            <p class="text-white text-sm font-semibold">عقد</p>
        </button>
        <button onclick="openAttachmentModal('رسومات هندسية')" class="bg-white/5 hover:bg-white/10 rounded-lg p-4 text-right border border-white/10 transition-all">
            <i class="fas fa-drafting-compass text-primary-400 text-2xl mb-2"></i>
            <p class="text-white text-sm font-semibold">رسومات هندسية</p>
        </button>
        <button onclick="openAttachmentModal('هوية/صك')" class="bg-white/5 hover:bg-white/10 rounded-lg p-4 text-right border border-white/10 transition-all">
            <i class="fas fa-id-card text-primary-400 text-2xl mb-2"></i>
            <p class="text-white text-sm font-semibold">هوية/صك</p>
        </button>
        <button onclick="openAttachmentModal('رفع مساحي')" class="bg-white/5 hover:bg-white/10 rounded-lg p-4 text-right border border-white/10 transition-all">
            <i class="fas fa-ruler-combined text-primary-400 text-2xl mb-2"></i>
            <p class="text-white text-sm font-semibold">رفع مساحي</p>
        </button>
    </div>

    @if($project->attachments->count() > 0)
    <!-- Attachments List -->
    <div class="space-y-3">
        @foreach($project->attachments as $attachment)
            @php
                $extension = pathinfo($attachment->file_path, PATHINFO_EXTENSION);
                $iconClasses = [
                    'pdf' => ['icon' => 'file-pdf', 'bg' => 'bg-red-500/20', 'color' => 'text-red-400'],
                    'jpg' => ['icon' => 'file-image', 'bg' => 'bg-blue-500/20', 'color' => 'text-blue-400'],
                    'jpeg' => ['icon' => 'file-image', 'bg' => 'bg-blue-500/20', 'color' => 'text-blue-400'],
                    'png' => ['icon' => 'file-image', 'bg' => 'bg-blue-500/20', 'color' => 'text-blue-400'],
                    'dwg' => ['icon' => 'file-code', 'bg' => 'bg-green-500/20', 'color' => 'text-green-400'],
                    'doc' => ['icon' => 'file-word', 'bg' => 'bg-blue-500/20', 'color' => 'text-blue-400'],
                    'docx' => ['icon' => 'file-word', 'bg' => 'bg-blue-500/20', 'color' => 'text-blue-400'],
                ];
                $fileConfig = $iconClasses[strtolower($extension)] ?? ['icon' => 'file', 'bg' => 'bg-gray-500/20', 'color' => 'text-gray-400'];
            @endphp
            <div class="bg-white/5 rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 {{ $fileConfig['bg'] }} rounded-lg flex items-center justify-center">
                        <i class="fas fa-{{ $fileConfig['icon'] }} {{ $fileConfig['color'] }} text-xl"></i>
                    </div>
                    <div>
                        <p class="text-white font-semibold">{{ \Illuminate\Support\Str::limit($attachment->name, 50) }}</p>
                        <div class="flex items-center gap-3 text-gray-400 text-xs mt-1">
                            @if($attachment->file_size)
                            <span>{{ number_format($attachment->file_size / 1024, 2) }} KB</span>
                            @endif
                            <span>•</span>
                            <span>رفع في {{ $attachment->created_at->format('Y-m-d') }}</span>
                            @if($attachment->uploader)
                            <span>•</span>
                            <span>من {{ $attachment->uploader->name }}</span>
                            @endif
                            @if($attachment->category)
                            <span>•</span>
                            <span>{{ $attachment->category }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-primary-400 hover:text-primary-300" title="تحميل">
                        <i class="fas fa-download"></i>
                    </a>
                    @can('update', $project)
                    <form action="{{ route('projects.attachments.destroy', [$project->id, $attachment->id]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-paperclip text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-bold text-white mb-2">لا توجد مرفقات</h3>
        <p class="text-gray-400 mb-4">ابدأ برفع الملفات الخاصة بالمشروع</p>
    </div>
    @endif
</div>

<!-- Attachment Upload Modal -->
<div id="attachmentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center" onclick="closeAttachmentModal(event)">
    <div class="glass-card rounded-xl md:rounded-2xl p-6 max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">رفع ملف</h3>
            <button onclick="closeAttachmentModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('projects.attachments.store', $project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">الملف</label>
                    <input type="file" name="file" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                </div>
                
                <div>
                    <label class="block text-gray-300 text-sm mb-2">اسم الملف</label>
                    <input type="text" name="name" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40" placeholder="سيتم استخدام اسم الملف إذا لم يتم تحديده">
                </div>
                
                <div>
                    <label class="block text-gray-300 text-sm mb-2">النوع/الفئة</label>
                    <select name="category" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                        <option value="عقد">عقد</option>
                        <option value="رسومات هندسية">رسومات هندسية</option>
                        <option value="هوية/صك">هوية/صك</option>
                        <option value="رفع مساحي">رفع مساحي</option>
                        <option value="مستندات أخرى">مستندات أخرى</option>
                    </select>
                </div>
            </div>
            
            <div class="flex items-center gap-3 mt-6">
                <button type="button" onclick="closeAttachmentModal()" class="flex-1 px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                    <i class="fas fa-upload ml-2"></i>
                    رفع
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let selectedCategory = '';

function openAttachmentModal(category = '') {
    selectedCategory = category;
    const modal = document.getElementById('attachmentModal');
    if (category && modal.querySelector('select[name="category"]')) {
        modal.querySelector('select[name="category"]').value = category;
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAttachmentModal(event) {
    if (!event || event.target === event.currentTarget || event.target.closest('button')) {
        document.getElementById('attachmentModal').classList.add('hidden');
        document.getElementById('attachmentModal').classList.remove('flex');
    }
}
</script>
