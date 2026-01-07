@extends('layouts.dashboard')

@section('title', 'تعديل بيانات الموظف - المنار')
@section('page-title', 'تعديل بيانات الموظف')

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
<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">تعديل بيانات الموظف</h1>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.show', $user->id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-eye ml-2"></i>
            عرض
        </a>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>
</div>

<!-- Form -->
<form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data" x-data="userForm()">
    @csrf
    @method('PUT')

    <!-- Basic Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">البيانات الأساسية</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">الاسم الكامل <span class="text-red-400">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    required
                    value="{{ old('name', $user->name) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="مثال: محمد أحمد العلي"
                >
                @error('name')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">البريد الإلكتروني <span class="text-red-400">*</span></label>
                <input 
                    type="email" 
                    name="email" 
                    required
                    value="{{ old('email', $user->email) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="example@manar.com"
                >
                @error('email')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">رقم الجوال</label>
                <input 
                    type="tel" 
                    name="phone" 
                    value="{{ old('phone', $user->phone) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="05XXXXXXXX"
                >
                @error('phone')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">رقم الهوية</label>
                <input 
                    type="text" 
                    name="national_id" 
                    value="{{ old('national_id', $user->national_id) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="10 أرقام"
                >
                @error('national_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">كلمة المرور (اتركه فارغاً إذا لم ترد التغيير)</label>
                <input 
                    type="password" 
                    name="password" 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">تأكيد كلمة المرور</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="••••••••"
                >
            </div>
        </div>
    </div>

    <!-- Job Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">البيانات الوظيفية</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">الوظيفة</label>
                <select name="job_title" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">اختر الوظيفة</option>
                    <option value="مهندس معماري" {{ old('job_title', $user->job_title) == 'مهندس معماري' ? 'selected' : '' }}>مهندس معماري</option>
                    <option value="مهندس إنشائي" {{ old('job_title', $user->job_title) == 'مهندس إنشائي' ? 'selected' : '' }}>مهندس إنشائي</option>
                    <option value="مهندس كهرباء" {{ old('job_title', $user->job_title) == 'مهندس كهرباء' ? 'selected' : '' }}>مهندس كهرباء</option>
                    <option value="مهندس ميكانيكي" {{ old('job_title', $user->job_title) == 'مهندس ميكانيكي' ? 'selected' : '' }}>مهندس ميكانيكي</option>
                    <option value="مدير مشروع" {{ old('job_title', $user->job_title) == 'مدير مشروع' ? 'selected' : '' }}>مدير مشروع</option>
                    <option value="إداري" {{ old('job_title', $user->job_title) == 'إداري' ? 'selected' : '' }}>إداري</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">رقم/ملف مزاولة المهنة</label>
                <div class="flex items-center gap-2">
                    <input 
                        type="text" 
                        name="practice_license_no" 
                        value="{{ old('practice_license_no', $user->practice_license_no) }}"
                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="رقم الرخصة"
                    >
                    <input 
                        type="file" 
                        name="practice_license_file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="hidden"
                        id="licenseFile"
                        @change="handleLicenseFile($event)"
                    >
                    <label for="licenseFile" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer text-sm">
                        <i class="fas fa-upload"></i>
                    </label>
                </div>
                @if($user->practice_license_file)
                <div class="mt-2">
                    <a href="{{ asset('storage/' . $user->practice_license_file) }}" target="_blank" class="text-primary-400 hover:text-primary-300 text-sm flex items-center gap-2">
                        <i class="fas fa-file-pdf"></i>
                        <span>عرض الملف الحالي</span>
                    </a>
                </div>
                @endif
                <p class="text-gray-400 text-xs mt-1" x-show="licenseFileName" x-text="licenseFileName"></p>
                @error('practice_license_file')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">تاريخ انتهاء تصنيف المهندس</label>
                <input 
                    type="date" 
                    name="engineer_rank_expiry" 
                    value="{{ old('engineer_rank_expiry', $user->engineer_rank_expiry ? $user->engineer_rank_expiry->format('Y-m-d') : '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">الحالة</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>معلق</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Roles & Avatar -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">الأدوار والصورة الشخصية</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">الأدوار <span class="text-red-400">*</span></label>
                <select name="roles[]" multiple required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 h-32">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $user->roles->contains('id', $role->id) ? 'selected' : '' }}>{{ $role->display_name }}</option>
                    @endforeach
                </select>
                <p class="text-gray-400 text-xs mt-1">يمكن اختيار عدة أدوار (اضغط Ctrl/CMD للاختيار المتعدد)</p>
                @error('roles')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">الصورة الشخصية (Avatar)</label>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 bg-primary-400/20 rounded-full flex items-center justify-center overflow-hidden">
                        <img x-show="avatarPreview" :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover">
                        <i x-show="!avatarPreview" class="fas fa-user-circle text-primary-400 text-4xl"></i>
                    </div>
                    <div class="flex-1">
                        <input 
                            type="file" 
                            name="avatar"
                            accept=".jpg,.jpeg,.png"
                            class="hidden"
                            id="avatarInput"
                            @change="handleAvatarSelect($event)"
                        >
                        <label for="avatarInput" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer transition-all duration-200 text-sm inline-block">
                            <i class="fas fa-upload ml-2"></i>
                            اختر صورة
                        </label>
                        <p class="text-gray-400 text-xs mt-1">JPG, PNG (حد أقصى 2MB)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            إلغاء
        </a>
        <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-save ml-2"></i>
            حفظ
        </button>
    </div>
</form>

@push('scripts')
<script>
function userForm() {
    return {
        avatarPreview: @if($user->avatar) '{{ asset("storage/" . $user->avatar) }}' @else null @endif,
        licenseFileName: null,
        init() {
            // Set initial avatar preview if exists
            @if($user->avatar)
            this.avatarPreview = '{{ asset("storage/" . $user->avatar) }}';
            @endif
        },
        handleAvatarSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.avatarPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        handleLicenseFile(event) {
            const file = event.target.files[0];
            if (file) {
                this.licenseFileName = file.name;
            }
        }
    }
}
</script>
@endpush

@endsection

