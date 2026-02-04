@php
    $documents = $project->documents ?? collect();
    $technicalReports = $documents->where('type', 'technical_report');
    $quotations = $documents->where('type', 'quotation');
@endphp

<div x-data="documentsTab()">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-white">مستندات المشروع</h2>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            @can('create', \App\Models\Document::class)
            <a href="{{ route('documents.create', ['type' => 'technical_report', 'project_id' => $project->id, 'client_id' => $project->client_id]) }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-file-alt ml-2"></i>
                تقرير فني جديد
            </a>
            <a href="{{ route('documents.create', ['type' => 'quotation', 'project_id' => $project->id, 'client_id' => $project->client_id]) }}" class="px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-file-invoice-dollar ml-2"></i>
                عرض سعر جديد
            </a>
            @endcan
        </div>
    </div>

    <!-- Tabs -->
    <div class="glass-card rounded-xl md:rounded-2xl mb-4 md:mb-6">
        <div class="border-b border-white/10 flex flex-wrap overflow-x-auto">
            <button 
                @click="activeType = 'technical_report'"
                :class="activeType === 'technical_report' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-file-alt"></i>
                التقارير الفنية
                @if($technicalReports->count() > 0)
                <span class="bg-primary-400/20 text-primary-400 px-2 py-0.5 rounded text-xs">{{ $technicalReports->count() }}</span>
                @endif
            </button>
            <button 
                @click="activeType = 'quotation'"
                :class="activeType === 'quotation' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-file-invoice-dollar"></i>
                عروض الأسعار
                @if($quotations->count() > 0)
                <span class="bg-[#1db8f8]/20 text-[#1db8f8] px-2 py-0.5 rounded text-xs">{{ $quotations->count() }}</span>
                @endif
            </button>
        </div>
    </div>

    <!-- Technical Reports -->
    <div x-show="activeType === 'technical_report'">
        @if($technicalReports->count() > 0)
        <div class="space-y-4">
            @foreach($technicalReports as $document)
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-file-alt text-2xl text-[#1db8f8]"></i>
                            <div>
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <a href="{{ route('documents.show', $document) }}" class="hover:text-[#1db8f8] transition-colors">
                                        {{ $document->title }}
                                    </a>
                                    @php
                                        $statusColors = ['draft' => 'gray', 'submitted' => 'yellow', 'approved' => 'green', 'rejected' => 'red'];
                                        $statusLabels = ['draft' => 'مسودة', 'submitted' => 'مرسل', 'approved' => 'معتمد', 'rejected' => 'مرفوض'];
                                        $color = $statusColors[$document->status] ?? 'blue';
                                        $label = $statusLabels[$document->status] ?? $document->status;
                                    @endphp
                                    <span class="bg-{{ $color }}-500/20 text-{{ $color }}-400 px-2 py-0.5 rounded text-xs">{{ $label }}</span>
                                </h3>
                                <p class="text-gray-400 text-sm mt-1">
                                    <i class="fas fa-hashtag ml-1"></i>
                                    {{ $document->document_number }}
                                </p>
                                <p class="text-gray-400 text-xs mt-1">
                                    <i class="fas fa-calendar ml-1"></i>
                                    {{ $document->created_at->format('Y-m-d') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('documents.show', $document) }}" class="px-3 py-2 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('update', $document)
                        <a href="{{ route('documents.edit', $document) }}" class="px-3 py-2 bg-[#1db8f8]/20 hover:bg-[#1db8f8]/30 text-[#1db8f8] rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-pen"></i>
                        </a>
                        @endcan
                        <a href="{{ route('documents.preview-pdf', $document) }}" target="_blank" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="glass-card rounded-xl md:rounded-2xl p-12 text-center">
            <i class="fas fa-file-alt text-6xl text-gray-500 mb-4"></i>
            <p class="text-gray-400 text-lg mb-4">لا توجد تقارير فنية</p>
            @can('create', \App\Models\Document::class)
            <a href="{{ route('documents.create', ['type' => 'technical_report', 'project_id' => $project->id, 'client_id' => $project->client_id]) }}" class="inline-block px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-plus ml-2"></i>
                إنشاء تقرير فني
            </a>
            @endcan
        </div>
        @endif
    </div>

    <!-- Quotations -->
    <div x-show="activeType === 'quotation'">
        @if($quotations->count() > 0)
        <div class="space-y-4">
            @foreach($quotations as $document)
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-file-invoice-dollar text-2xl text-[#1db8f8]"></i>
                            <div>
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <a href="{{ route('documents.show', $document) }}" class="hover:text-[#1db8f8] transition-colors">
                                        {{ $document->title }}
                                    </a>
                                    @php
                                        $statusColors = ['draft' => 'gray', 'sent' => 'yellow', 'accepted' => 'green', 'rejected' => 'red', 'expired' => 'gray'];
                                        $statusLabels = ['draft' => 'مسودة', 'sent' => 'مرسل', 'accepted' => 'مقبول', 'rejected' => 'مرفوض', 'expired' => 'منتهي'];
                                        $color = $statusColors[$document->status] ?? 'blue';
                                        $label = $statusLabels[$document->status] ?? $document->status;
                                    @endphp
                                    <span class="bg-{{ $color }}-500/20 text-{{ $color }}-400 px-2 py-0.5 rounded text-xs">{{ $label }}</span>
                                </h3>
                                <p class="text-gray-400 text-sm mt-1">
                                    <i class="fas fa-hashtag ml-1"></i>
                                    {{ $document->document_number }}
                                </p>
                                <div class="flex items-center gap-4 text-sm text-gray-400 mt-1">
                                    @if($document->total_price)
                                    <span class="text-[#1db8f8] font-semibold">
                                        <i class="fas fa-money-bill-wave ml-1"></i>
                                        {{ number_format($document->total_price, 2) }} ر.س
                                    </span>
                                    @endif
                                    <span>
                                        <i class="fas fa-calendar ml-1"></i>
                                        {{ $document->created_at->format('Y-m-d') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('documents.show', $document) }}" class="px-3 py-2 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('update', $document)
                        <a href="{{ route('documents.edit', $document) }}" class="px-3 py-2 bg-[#1db8f8]/20 hover:bg-[#1db8f8]/30 text-[#1db8f8] rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-pen"></i>
                        </a>
                        @endcan
                        <a href="{{ route('documents.preview-pdf', $document) }}" target="_blank" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="glass-card rounded-xl md:rounded-2xl p-12 text-center">
            <i class="fas fa-file-invoice-dollar text-6xl text-gray-500 mb-4"></i>
            <p class="text-gray-400 text-lg mb-4">لا توجد عروض أسعار</p>
            @can('create', \App\Models\Document::class)
            <a href="{{ route('documents.create', ['type' => 'quotation', 'project_id' => $project->id, 'client_id' => $project->client_id]) }}" class="inline-block px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-plus ml-2"></i>
                إنشاء عرض سعر
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function documentsTab() {
    return {
        activeType: 'technical_report',
    }
}
</script>
@endpush
