<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">المرفقات</h2>
        <button onclick="openAttachmentModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
            <i class="fas fa-plus ml-2"></i>
            رفع ملف
        </button>
    </div>

    <!-- Upload Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <button class="bg-white/5 hover:bg-white/10 rounded-lg p-4 text-right border border-white/10 transition-all">
            <i class="fas fa-file-contract text-primary-400 text-2xl mb-2"></i>
            <p class="text-white text-sm font-semibold">عقد</p>
        </button>
        <button class="bg-white/5 hover:bg-white/10 rounded-lg p-4 text-right border border-white/10 transition-all">
            <i class="fas fa-drafting-compass text-primary-400 text-2xl mb-2"></i>
            <p class="text-white text-sm font-semibold">رسومات هندسية</p>
        </button>
        <button class="bg-white/5 hover:bg-white/10 rounded-lg p-4 text-right border border-white/10 transition-all">
            <i class="fas fa-id-card text-primary-400 text-2xl mb-2"></i>
            <p class="text-white text-sm font-semibold">هوية/صك</p>
        </button>
        <button class="bg-white/5 hover:bg-white/10 rounded-lg p-4 text-right border border-white/10 transition-all">
            <i class="fas fa-ruler-combined text-primary-400 text-2xl mb-2"></i>
            <p class="text-white text-sm font-semibold">رفع مساحي</p>
        </button>
    </div>

    <!-- Attachments List -->
    <div class="space-y-3">
        <div class="bg-white/5 rounded-lg p-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-pdf text-red-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-white font-semibold">عقد المشروع.pdf</p>
                    <p class="text-gray-400 text-xs">2.5 MB • رفع في 2025-01-15</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button class="text-primary-400 hover:text-primary-300"><i class="fas fa-download"></i></button>
                <button class="text-red-400 hover:text-red-300"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <div class="bg-white/5 rounded-lg p-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-image text-blue-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-white font-semibold">المخطط المعماري.dwg</p>
                    <p class="text-gray-400 text-xs">5.2 MB • رفع في 2025-02-01</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button class="text-primary-400 hover:text-primary-300"><i class="fas fa-download"></i></button>
                <button class="text-red-400 hover:text-red-300"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
function openAttachmentModal() {
    alert('سيتم فتح نافذة رفع ملف');
}
</script>
