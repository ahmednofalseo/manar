@extends('layouts.dashboard')

@section('title', 'إنشاء قالب مسار - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'إنشاء قالب مسار')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .step-item {
        cursor: move;
    }
    .step-item:hover {
        background: rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@section('content')
<!-- Toast Notifications -->
@if(session('error'))
<div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<div x-data="workflowBuilder()" class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">إنشاء قالب مسار جديد</h1>
        <a href="{{ route('workflow-templates.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('workflow-templates.store') }}" method="POST" @submit.prevent="submitForm">
        @csrf

        <div class="space-y-6">
            <!-- Template Info -->
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-xl font-bold text-white mb-4">معلومات القالب</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="select-wrapper">
                        <label class="block text-gray-300 text-sm mb-2">الخدمة <span class="text-red-400">*</span></label>
                        <select 
                            name="service_id" 
                            x-model="serviceId"
                            required
                            class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200"
                            style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;"
                        >
                            <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">اختر الخدمة</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id', $serviceId) == $service->id ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">
                                {{ $service->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('service_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm mb-2">اسم القالب <span class="text-red-400">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            x-model="templateName"
                            required
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            placeholder="مثال: مسار تصميم كامل"
                        >
                        @error('name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-300 text-sm mb-2">الوصف</label>
                        <textarea 
                            name="description" 
                            x-model="templateDescription"
                            rows="2"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            placeholder="وصف مختصر للقالب..."
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_default" 
                                value="1"
                                x-model="isDefault"
                                class="w-5 h-5 rounded border-white/10 bg-white/5 text-primary-500 focus:ring-primary-400/40"
                            >
                            <span class="text-gray-300">قالب افتراضي</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                value="1"
                                x-model="isActive"
                                checked
                                class="w-5 h-5 rounded border-white/10 bg-white/5 text-primary-500 focus:ring-primary-400/40"
                            >
                            <span class="text-gray-300">نشط</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Steps Builder -->
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-white">خطوات المسار</h2>
                    <button 
                        type="button"
                        @click="addStep()"
                        class="px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200"
                    >
                        <i class="fas fa-plus ml-2"></i>
                        إضافة خطوة
                    </button>
                </div>

                <div id="steps-container" class="space-y-4">
                    <template x-for="(step, index) in steps" :key="index">
                        <div 
                            class="step-item bg-white/5 rounded-lg p-4 border border-white/10"
                            :data-index="index"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-grip-vertical text-gray-400 cursor-move"></i>
                                        <span class="text-[#1db8f8] font-bold" x-text="'خطوة ' + (index + 1)"></span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-300 text-sm mb-2">اسم الخطوة <span class="text-red-400">*</span></label>
                                            <input 
                                                type="text" 
                                                x-model="step.name"
                                                required
                                                class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm"
                                                placeholder="مثال: التصميم المعماري"
                                            >
                                        </div>

                                        <div class="select-wrapper">
                                            <label class="block text-gray-300 text-sm mb-2">القسم <span class="text-red-400">*</span></label>
                                            <select 
                                                x-model="step.department"
                                                required
                                                class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm"
                                                style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;"
                                            >
                                                @foreach($departments as $key => $label)
                                                <option value="{{ $key }}" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-gray-300 text-sm mb-2">المدة بالأيام <span class="text-red-400">*</span></label>
                                            <input 
                                                type="number" 
                                                x-model.number="step.duration"
                                                required
                                                min="1"
                                                step="1"
                                                class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm"
                                                placeholder="7"
                                            >
                                        </div>

                                        <div>
                                            <label class="block text-gray-300 text-sm mb-2">الترتيب</label>
                                            <input 
                                                type="number" 
                                                x-model.number="step.order"
                                                min="0"
                                                readonly
                                                class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm opacity-60 cursor-not-allowed"
                                                :value="index"
                                            >
                                            <p class="text-xs text-gray-400 mt-1">سيتم تحديث الترتيب تلقائياً عند السحب</p>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-gray-300 text-sm mb-2">الوصف</label>
                                        <textarea 
                                            x-model="step.description"
                                            rows="2"
                                            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm"
                                            placeholder="وصف الخطوة..."
                                        ></textarea>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                x-model="step.is_parallel"
                                                class="w-4 h-4 rounded border-white/10 bg-white/5 text-primary-500 focus:ring-primary-400/40"
                                            >
                                            <span class="text-gray-300 text-sm">
                                                <i class="fas fa-sitemap ml-1 text-[#1db8f8]"></i>
                                                يمكن تنفيذها بالتوازي
                                            </span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                x-model="step.is_required"
                                                checked
                                                class="w-4 h-4 rounded border-white/10 bg-white/5 text-primary-500 focus:ring-primary-400/40"
                                            >
                                            <span class="text-gray-300 text-sm">
                                                <i class="fas fa-check-circle ml-1 text-green-400"></i>
                                                خطوة مطلوبة
                                            </span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                x-model="step.requires_approval"
                                                class="w-4 h-4 rounded border-white/10 bg-white/5 text-primary-500 focus:ring-primary-400/40"
                                            >
                                            <span class="text-gray-300 text-sm">
                                                <i class="fas fa-check-double ml-1 text-yellow-400"></i>
                                                تتطلب موافقة
                                            </span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                x-model="step.requires_files"
                                                class="w-4 h-4 rounded border-white/10 bg-white/5 text-primary-500 focus:ring-primary-400/40"
                                            >
                                            <span class="text-gray-300 text-sm">
                                                <i class="fas fa-file-upload ml-1 text-blue-400"></i>
                                                تتطلب ملفات
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <button 
                                        type="button"
                                        @click="removeStep(index)"
                                        class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="steps.length === 0" class="text-center py-8 text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>لا توجد خطوات. اضغط "إضافة خطوة" لبدء البناء</p>
                    </div>
                </div>
            </div>

            <!-- Hidden Inputs for Steps -->
            <template x-for="(step, index) in steps" :key="index">
                <div>
                    <input type="hidden" :name="`steps[${index}][name]`" :value="step.name || ''">
                    <input type="hidden" :name="`steps[${index}][description]`" :value="step.description || ''">
                    <input type="hidden" :name="`steps[${index}][order]`" :value="index">
                    <input type="hidden" :name="`steps[${index}][department]`" :value="step.department || ''">
                    <input type="hidden" :name="`steps[${index}][default_duration_days]`" :value="step.duration || 0">
                    <input type="hidden" :name="`steps[${index}][is_parallel]`" :value="step.is_parallel ? 1 : 0">
                    <input type="hidden" :name="`steps[${index}][is_required]`" :value="step.is_required !== false ? 1 : 0">
                    <input type="hidden" :name="`steps[${index}][expected_outputs]`" :value="JSON.stringify({requires_approval: step.requires_approval || false, requires_files: step.requires_files || false})">
                </div>
            </template>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <a href="{{ route('workflow-templates.index') }}" class="px-6 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                    إلغاء
                </a>
                <button 
                    type="submit" 
                    :disabled="steps.length === 0 || loading"
                    :class="(steps.length === 0 || loading) ? 'opacity-50 cursor-not-allowed' : ''"
                    class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200"
                >
                    <i class="fas fa-save ml-2"></i>
                    <span x-show="!loading">حفظ القالب</span>
                    <span x-show="loading">جاري الحفظ...</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
function workflowBuilder() {
    return {
        serviceId: '{{ old("service_id", $serviceId ?? "") }}',
        templateName: '{{ old("name", "") }}',
        templateDescription: '{{ old("description", "") }}',
        isDefault: {{ old("is_default", false) ? 'true' : 'false' }},
        isActive: {{ old("is_active", true) ? 'true' : 'false' }},
        steps: @json(old('steps', [])),
        loading: false,
        sortableInstance: null,

        init() {
            // Initialize Sortable after a short delay to ensure DOM is ready
            this.$nextTick(() => {
                if (this.steps.length > 0) {
                    this.initSortable();
                }
            });
        },

        addStep() {
            const newStep = {
                name: '',
                description: '',
                department: 'معماري',
                duration: 7,
                order: this.steps.length,
                is_parallel: false,
                is_required: true,
                requires_approval: false,
                requires_files: false,
            };
            this.steps.push(newStep);
            
            this.$nextTick(() => {
                this.initSortable();
            });
        },

        removeStep(index) {
            if (confirm('هل أنت متأكد من حذف هذه الخطوة؟')) {
                this.steps.splice(index, 1);
                // Update orders
                this.steps.forEach((step, i) => {
                    step.order = i;
                });
                
                // Reinitialize Sortable after removal
                this.$nextTick(() => {
                    this.initSortable();
                });
            }
        },

        initSortable() {
            const container = document.getElementById('steps-container');
            if (!container) return;

            // Destroy existing Sortable instance if any
            if (this.sortableInstance) {
                this.sortableInstance.destroy();
            }

            this.sortableInstance = new Sortable(container, {
                animation: 150,
                handle: '.fa-grip-vertical',
                onEnd: (evt) => {
                    // Reorder steps array
                    const oldIndex = evt.oldIndex;
                    const newIndex = evt.newIndex;
                    const movedStep = this.steps.splice(oldIndex, 1)[0];
                    this.steps.splice(newIndex, 0, movedStep);
                    
                    // Update orders after drag
                    this.steps.forEach((step, i) => {
                        step.order = i;
                    });
                }
            });
        },

        submitForm(event) {
            event.preventDefault();
            
            if (this.steps.length === 0) {
                alert('يجب إضافة خطوة واحدة على الأقل');
                return false;
            }

            // Validate service
            if (!this.serviceId || this.serviceId === '') {
                alert('يرجى اختيار الخدمة');
                return false;
            }

            // Validate template name
            if (!this.templateName || this.templateName.trim() === '') {
                alert('يرجى إدخال اسم القالب');
                return false;
            }

            // Validate all steps
            for (let i = 0; i < this.steps.length; i++) {
                const step = this.steps[i];
                
                // Trim and check name
                if (!step.name || (typeof step.name === 'string' && step.name.trim() === '')) {
                    alert(`يرجى إدخال اسم الخطوة ${i + 1}`);
                    return false;
                }
                
                // Check department
                if (!step.department || (typeof step.department === 'string' && step.department.trim() === '')) {
                    alert(`يرجى اختيار القسم للخطوة ${i + 1}`);
                    return false;
                }
                
                // Check duration - convert to number and check if valid
                let duration = step.duration;
                if (typeof duration === 'string') {
                    duration = parseInt(duration);
                }
                if (typeof duration !== 'number' || isNaN(duration) || duration < 1) {
                    alert(`يرجى إدخال مدة صحيحة (أكبر من 0) للخطوة ${i + 1}`);
                    return false;
                }
                
                // Update step with trimmed values and numeric duration
                if (typeof step.name === 'string') {
                    step.name = step.name.trim();
                }
                step.duration = duration;
                step.order = i;
            }

            // Show loading state
            this.loading = true;
            
            // Submit form
            const form = event.target;
            form.submit();
        }
    }
}
</script>
@endpush
@endsection
