@php
    $plannerRows = old('stage_plan');
    if ($plannerRows === null && isset($project)) {
        $plannerRows = $project->projectStages->sortBy('sort_order')->values()->map(fn ($stage) => [
            'id' => $stage->id,
            'name' => $stage->stage_name,
            'amount' => (float) $stage->invoice_amount,
        ])->all();
    }
    $plannerRows = collect($plannerRows ?? [])->map(function ($row) {
        return [
            'id' => $row['id'] ?? null,
            'name' => $row['name'] ?? '',
            'amount' => $row['amount'] ?? 0,
            'key' => ($row['id'] ?? 'new').'-'.($row['name'] ?? uniqid()),
        ];
    })->values();

    $stageCatalog = collect($stages ?? [])->map(function ($stage) {
        $label = app()->getLocale() === 'en' && filled($stage->name_en ?? null)
            ? $stage->name_en
            : $stage->name;

        return ['name' => $stage->name, 'label' => $label];
    })->values();
@endphp

<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6" x-data="stagePlanner(@js($plannerRows), @js($stageCatalog))">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-diagram-project text-primary-400"></i>
                {{ __('Stages') }}
            </h2>
            <p class="text-gray-400 text-sm mt-1">{{ __('Stage planner hint') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <select x-model="catalogPick" class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm">
                <option value="">{{ __('Choose stage') }}</option>
                <template x-for="item in catalog" :key="item.name">
                    <option :value="item.name" x-text="item.label"></option>
                </template>
            </select>
            <button type="button" @click="addFromCatalog()" class="px-3 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
                {{ __('Add') }}
            </button>
        </div>
    </div>

    <div class="space-y-3" @dragover.prevent>
        <template x-for="(stage, index) in stages" :key="stage.key">
            <div
                class="flex flex-col md:flex-row md:items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10"
                draggable="true"
                @dragstart="dragIndex = index"
                @dragover.prevent
                @drop="move(dragIndex, index)"
            >
                <input type="hidden" :name="`stage_plan[${index}][id]`" :value="stage.id || ''">
                <button type="button" class="text-gray-400 cursor-grab px-1" title="{{ __('Drag to reorder') }}">
                    <i class="fas fa-grip-vertical"></i>
                </button>
                <div class="w-8 text-center text-primary-400 font-semibold" x-text="index + 1"></div>
                <div class="flex-1">
                    <label class="block text-gray-400 text-xs mb-1">{{ __('Stage') }}</label>
                    <input type="text" :name="`stage_plan[${index}][name]`" x-model="stage.name" required class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
                <div class="w-full md:w-48">
                    <label class="block text-gray-400 text-xs mb-1">{{ __('Stage invoice amount') }}</label>
                    <input type="number" min="0" step="0.01" :name="`stage_plan[${index}][amount]`" x-model="stage.amount" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
                </div>
                <button type="button" @click="remove(index)" class="px-3 py-2 bg-red-500/20 text-red-400 rounded-lg text-sm">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </template>
        <p x-show="stages.length === 0" class="text-gray-400 text-sm">{{ __('No stages yet') }}</p>
    </div>
    @error('stage_plan')
        <p class="mt-3 text-sm text-red-400">{{ $message }}</p>
    @enderror
    @error('stage_plan.*.name')
        <p class="mt-3 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
<script>
function stagePlanner(initial, catalog) {
    return {
        stages: initial || [],
        catalog: catalog || [],
        catalogPick: '',
        dragIndex: null,
        addFromCatalog() {
            if (!this.catalogPick) return;
            this.stages.push({
                id: null,
                name: this.catalogPick,
                amount: 0,
                key: 'new-' + Date.now()
            });
            this.catalogPick = '';
        },
        remove(index) {
            this.stages.splice(index, 1);
        },
        move(from, to) {
            if (from === null || from === to) return;
            const item = this.stages.splice(from, 1)[0];
            this.stages.splice(to, 0, item);
            this.dragIndex = null;
        }
    };
}
</script>
@endpush
