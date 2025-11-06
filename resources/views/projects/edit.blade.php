@extends('layouts.dashboard')

@section('title', 'تعديل المشروع - المنار')
@section('page-title', 'تعديل المشروع')

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
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">تعديل المشروع</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.show', $id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-eye ml-2"></i>
                عرض
            </a>
            <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('projects.update', $id) }}" enctype="multipart/form-data" x-data="projectForm()">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-info-circle text-primary-400"></i>
                البيانات الأساسية
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">اسم المشروع <span class="text-red-400">*</span></label>
                    <input 
                        type="text" 
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="مثال: فيلا سكنية - العليا"
                    >
                    @error('name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">رقم المشروع</label>
                    <input 
                        type="text" 
                        name="project_number"
                        value="{{ old('project_number') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="سيتم التوليد تلقائياً"
                    >
                    @error('project_number')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">نوع المشروع <span class="text-red-400">*</span></label>
                    <select 
                        name="type"
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                        <option value="">اختر نوع المشروع</option>
                        <option value="تصميم" {{ old('type') == 'تصميم' ? 'selected' : '' }}>تصميم</option>
                        <option value="تصميم وإشراف" {{ old('type') == 'تصميم وإشراف' ? 'selected' : '' }}>تصميم وإشراف</option>
                        <option value="إشراف" {{ old('type') == 'إشراف' ? 'selected' : '' }}>إشراف</option>
                        <option value="تقرير فني" {{ old('type') == 'تقرير فني' ? 'selected' : '' }}>تقرير فني</option>
                        <option value="تقرير دفاع مدني" {{ old('type') == 'تقرير دفاع مدني' ? 'selected' : '' }}>تقرير دفاع مدني</option>
                        <option value="تصميم دفاع مدني" {{ old('type') == 'تصميم دفاع مدني' ? 'selected' : '' }}>تصميم دفاع مدني</option>
                        <option value="تعديلات" {{ old('type') == 'تعديلات' ? 'selected' : '' }}>تعديلات</option>
                        <option value="استشارات" {{ old('type') == 'استشارات' ? 'selected' : '' }}>استشارات</option>
                    </select>
                    @error('type')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">المدينة <span class="text-red-400">*</span></label>
                    <select 
                        name="city"
                        required
                        x-model="city"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                        <option value="">اختر المدينة</option>
                        <option value="الرياض" {{ old('city') == 'الرياض' ? 'selected' : '' }}>الرياض</option>
                        <option value="جدة" {{ old('city') == 'جدة' ? 'selected' : '' }}>جدة</option>
                        <option value="الدمام" {{ old('city') == 'الدمام' ? 'selected' : '' }}>الدمام</option>
                        <option value="مكة" {{ old('city') == 'مكة' ? 'selected' : '' }}>مكة</option>
                        <option value="المدينة" {{ old('city') == 'المدينة' ? 'selected' : '' }}>المدينة</option>
                    </select>
                    @error('city')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">الحي</label>
                    <select 
                        name="district"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                        <option value="">اختر الحي</option>
                        <template x-if="city === 'الرياض'">
                            <template>
                                <option value="العليا">العليا</option>
                                <option value="الملك فهد">الملك فهد</option>
                                <option value="المرسلات">المرسلات</option>
                                <option value="الملز">الملز</option>
                            </template>
                        </template>
                        <template x-if="city === 'جدة'">
                            <template>
                                <option value="النخيل">النخيل</option>
                                <option value="الكورنيش">الكورنيش</option>
                                <option value="الروابي">الروابي</option>
                            </template>
                        </template>
                    </select>
                    @error('district')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">المالك <span class="text-red-400">*</span></label>
                    <input 
                        type="text" 
                        name="owner"
                        value="{{ old('owner') }}"
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="اسم المالك"
                    >
                    @error('owner')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">قيمة المشروع <span class="text-red-400">*</span></label>
                    <input 
                        type="number" 
                        name="value"
                        value="{{ old('value') }}"
                        required
                        step="0.01"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="0.00"
                    >
                    @error('value')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">رقم العقد</label>
                    <input 
                        type="text" 
                        name="contract_number"
                        value="{{ old('contract_number') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    @error('contract_number')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">ملف العقد</label>
                    <input 
                        type="file" 
                        name="contract_file"
                        accept=".pdf,.doc,.docx"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    @error('contract_file')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">رقم/كود الأرض</label>
                    <input 
                        type="text" 
                        name="land_number"
                        value="{{ old('land_number') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    @error('land_number')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">المخطط</label>
                    <input 
                        type="file" 
                        name="plan_file"
                        accept=".pdf,.jpg,.jpeg,.png,.dwg"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    @error('plan_file')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Platforms & Third Party -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-link text-primary-400"></i>
                المنصات والطرف الثالث
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">رقم طلب منصة بلدي</label>
                    <input 
                        type="text" 
                        name="baladi_request_number"
                        value="{{ old('baladi_request_number') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    @error('baladi_request_number')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Third Party Repeater -->
            <div x-data="thirdPartyRepeater()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">الطرف الثالث</h3>
                    <button type="button" @click="addItem()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة
                    </button>
                </div>
                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-gray-300 text-sm mb-2">الاسم/الجهة</label>
                                    <input 
                                        type="text" 
                                        :name="`third_party[${index}][name]`"
                                        x-model="item.name"
                                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                                    >
                                </div>
                                <div>
                                    <label class="block text-gray-300 text-sm mb-2">التاريخ</label>
                                    <input 
                                        type="date" 
                                        :name="`third_party[${index}][date]`"
                                        x-model="item.date"
                                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                                    >
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="removeItem(index)" class="w-full px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-sm">
                                        <i class="fas fa-trash ml-2"></i>
                                        حذف
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Stages -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-diagram-project text-primary-400"></i>
                المراحل
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="معماري" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">معماري</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="إنشائي" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">إنشائي</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="كهربائي" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">كهربائي</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="ميكانيكي" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">ميكانيكي</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="تقديم للبلدية" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">تقديم للبلدية</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="أخرى" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">أخرى</span>
                </label>
            </div>
        </div>

        <!-- Team Assignment -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-users text-primary-400"></i>
                إسناد الفريق
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">مدير المشروع</label>
                    <select 
                        name="project_manager"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                        <option value="">اختر مدير المشروع</option>
                        <option value="1">محمد أحمد</option>
                        <option value="2">فاطمة سالم</option>
                        <option value="3">خالد مطر</option>
                    </select>
                    @error('project_manager')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">المهندسون/الفنيون</label>
                    <select 
                        name="engineers[]"
                        multiple
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        size="4"
                    >
                        <option value="1">محمد أحمد - مهندس معماري</option>
                        <option value="2">فاطمة سالم - مهندسة إنشائية</option>
                        <option value="3">خالد مطر - مهندس كهرباء</option>
                        <option value="4">سارة علي - مهندسة ميكانيكا</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-400">اضغط Ctrl/Command لاختيار أكثر من واحد</p>
                    @error('engineers')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Internal Notes -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-sticky-note text-primary-400"></i>
                ملاحظات داخلية
            </h2>

            <textarea 
                name="internal_notes"
                rows="4"
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                placeholder="ملاحظات داخلية حول المشروع..."
            >{{ old('internal_notes') }}</textarea>
            @error('internal_notes')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('projects.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-save ml-2"></i>
                حفظ المشروع
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function projectForm() {
        return {
            city: '{{ old("city", "") }}'
        }
    }

    function thirdPartyRepeater() {
        return {
            items: [],
            addItem() {
                this.items.push({ name: '', date: '' });
            },
            removeItem(index) {
                this.items.splice(index, 1);
            }
        }
    }
</script>
@endpush

@endsection
