@extends('layouts.dashboard')

@section('title', __('Approval Management') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Approval Management'))

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@section('content')
<!-- Toast Notifications -->
@if(session('success'))
<div class="fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white animate-slide-in">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<!-- Header -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Approval Management') }}</h1>
</div>

<!-- Statistics -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Pending') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-yellow-400 mt-2">{{ $stats['pending'] }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-yellow-500/20 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Approved') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-green-400 mt-2">{{ $stats['approved'] }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Rejected') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-red-400 mt-2">{{ $stats['rejected'] }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-red-500/20 rounded-full flex items-center justify-center">
                <i class="fas fa-times-circle text-red-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Tabs -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-6">
    <!-- Tabs -->
    <div class="flex items-center gap-2 mb-4 border-b border-white/10">
        <a href="{{ route('approvals.index', ['status' => 'pending'] + request()->except('status')) }}" 
           class="px-4 py-2 {{ $status === 'pending' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white' }} transition-colors">
            {{ __('Pending') }} ({{ $stats['pending'] }})
        </a>
        <a href="{{ route('approvals.index', ['status' => 'approved'] + request()->except('status')) }}" 
           class="px-4 py-2 {{ $status === 'approved' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white' }} transition-colors">
            {{ __('Approved') }} ({{ $stats['approved'] }})
        </a>
        <a href="{{ route('approvals.index', ['status' => 'rejected'] + request()->except('status')) }}" 
           class="px-4 py-2 {{ $status === 'rejected' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white' }} transition-colors">
            {{ __('Rejected') }} ({{ $stats['rejected'] }})
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('approvals.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="hidden" name="status" value="{{ $status }}">
        
        <div>
            <label class="block text-gray-300 text-sm mb-2">{{ __('Project') }}</label>
            <select name="project_id" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                <option value="">{{ __('All Projects') }}</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                        {{ $project->name }} ({{ $project->project_number }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-gray-300 text-sm mb-2">{{ __('Stage') }}</label>
            <select name="stage_key" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                <option value="">{{ __('All Stages') }}</option>
                @foreach($stages as $stage)
                    <option value="{{ $stage['value'] }}" {{ $stageKey == $stage['value'] ? 'selected' : '' }}>
                        {{ $stage['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-filter {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Apply') }}
            </button>
            <a href="{{ route('approvals.index', ['status' => $status]) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-times {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Clear') }}
            </a>
        </div>
    </form>
</div>

<!-- Approvals Table -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    @if($approvals->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Project') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Stage') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Item') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Status') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Requested By') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Request Date') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($approvals as $approval)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3 text-white text-sm">
                            <div>
                                <div class="font-semibold">{{ $approval->project->name }}</div>
                                <div class="text-gray-400 text-xs">{{ $approval->project->project_number }}</div>
                            </div>
                        </td>
                        <td class="py-3 text-gray-300 text-sm">{{ $approval->stage_name }}</td>
                        <td class="py-3 text-gray-300 text-sm">
                            @if($approval->approvable_type === 'App\Models\ProjectAttachment')
                                <i class="fas fa-file {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i> {{ __('Attachment') }}
                            @elseif($approval->approvable_type === 'App\Models\ProjectStage')
                                <i class="fas fa-layer-group {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i> {{ __('Stage') }}
                            @else
                                {{ class_basename($approval->approvable_type) }}
                            @endif
                        </td>
                        <td class="py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold 
                                {{ $approval->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                {{ $approval->status === 'approved' ? 'bg-green-500/20 text-green-400' : '' }}
                                {{ $approval->status === 'rejected' ? 'bg-red-500/20 text-red-400' : '' }}
                            ">
                                {{ $approval->status_label }}
                            </span>
                        </td>
                        <td class="py-3 text-gray-300 text-sm">{{ $approval->requester->name }}</td>
                        <td class="py-3 text-gray-300 text-sm">{{ $approval->requested_at->format('Y-m-d H:i') }}</td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('approvals.show', $approval->id) }}" class="text-primary-400 hover:text-primary-300" :title="'{{ __('View') }}'">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($approval->status === 'pending' && auth()->user()->can('decide', $approval))
                                    <button onclick="openDecideModal({{ $approval->id }}, 'approve')" class="text-green-400 hover:text-green-300" :title="'{{ __('Approve') }}'">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="openDecideModal({{ $approval->id }}, 'reject')" class="text-red-400 hover:text-red-300" :title="'{{ __('Reject') }}'">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($approvals->hasPages())
    <div class="mt-6">
        {{ $approvals->links() }}
    </div>
    @endif

    @else
    <div class="text-center py-12">
        <i class="fas fa-clipboard-check text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-400 text-lg">{{ __('No approvals found') }}</p>
    </div>
    @endif
</div>

<!-- Decide Modal -->
@include('approvals.modals.decide')

@push('scripts')
<script>
function openDecideModal(approvalId, decision) {
    window.dispatchEvent(new CustomEvent('open-decide-modal', { 
        detail: { approvalId: approvalId, decision: decision } 
    }));
}
</script>
@endpush

@endsection



