@extends('layouts.dashboard')

@section('title', 'إنشاء مستند جديد - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'إنشاء مستند جديد')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .editor-toolbar {
        background: rgba(23, 51, 67, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px 8px 0 0;
        padding: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    .editor-toolbar button {
        padding: 6px 12px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
    }
    .editor-toolbar button:hover {
        background: rgba(29, 184, 248, 0.2);
        border-color: #1db8f8;
    }
    .editor-toolbar button.active {
        background: rgba(29, 184, 248, 0.3);
        border-color: #1db8f8;
    }
    .editor-content {
        min-height: 400px;
        background: white;
        color: #333;
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-top: none;
        border-radius: 0 0 8px 8px;
        direction: rtl;
        text-align: right;
        font-family: 'Cairo', sans-serif;
    }
    .editor-content:focus {
        outline: 2px solid #1db8f8;
        outline-offset: -2px;
    }
    .variables-dropdown {
        position: relative;
    }
    .variables-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: rgba(23, 51, 67, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 8px;
        min-width: 200px;
        z-index: 50;
        display: none;
    }
    .variables-menu.show {
        display: block;
    }
    .variables-menu button {
        display: block;
        width: 100%;
        text-align: right;
        padding: 8px 12px;
        background: transparent;
        border: none;
        color: white;
        cursor: pointer;
        border-radius: 4px;
        transition: all 0.2s;
    }
    .variables-menu button:hover {
        background: rgba(29, 184, 248, 0.2);
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

<div x-data="documentEditor()" class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">
            إنشاء {{ $type === 'technical_report' ? 'تقرير فني' : 'عرض سعر' }} جديد
        </h1>
        <a href="{{ route('documents.index', ['type' => $type]) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('documents.store') }}" method="POST" @submit.prevent="submitForm">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <h2 class="text-xl font-bold text-white mb-4">المعلومات الأساسية</h2>
                    <div class="space-y-4">
                        <div class="select-wrapper">
                            <label class="block text-gray-300 text-sm mb-2">القالب</label>
                            <select 
                                name="template_id" 
                                x-model="templateId"
                                @change="loadTemplate()"
                                class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200"
                                style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;"
                            >
                                <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">بدون قالب</option>
                                @foreach($templates as $template)
                                <option value="{{ $template->id }}" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-300 text-sm mb-2">عنوان المستند <span class="text-red-400">*</span></label>
                            <input 
                                type="text" 
                                name="title" 
                                x-model="title"
                                required
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                                placeholder="مثال: تقرير فني لمشروع..."
                            >
                            @error('title')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="select-wrapper">
                                <label class="block text-gray-300 text-sm mb-2">المشروع</label>
                                <select 
                                    name="project_id" 
                                    x-model="projectId"
                                    @change="updateClientFromProject()"
                                    class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200"
                                    style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;"
                                >
                                    <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">اختر المشروع</option>
                                    @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ old('project_id', $projectId) == $proj->id ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $proj->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="select-wrapper">
                                <label class="block text-gray-300 text-sm mb-2">العميل</label>
                                <select 
                                    name="client_id" 
                                    x-model="clientId"
                                    class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200"
                                    style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;"
                                >
                                    <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">اختر العميل</option>
                                    @foreach($clients as $cli)
                                    <option value="{{ $cli->id }}" {{ old('client_id', $clientId ?? '') == $cli->id ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $cli->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($type === 'quotation')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-300 text-sm mb-2">السعر الإجمالي</label>
                                <input 
                                    type="number" 
                                    name="total_price" 
                                    step="0.01"
                                    min="0"
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                                    placeholder="0.00"
                                >
                            </div>
                            <div>
                                <label class="block text-gray-300 text-sm mb-2">تاريخ الانتهاء</label>
                                <input 
                                    type="date" 
                                    name="expires_at" 
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                                >
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Document Editor -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <h2 class="text-xl font-bold text-white mb-4">محتوى المستند</h2>
                    
                    <!-- Editor Toolbar -->
                    <div class="editor-toolbar">
                        <button type="button" @click="formatDoc('bold')" title="عريض">
                            <i class="fas fa-bold"></i>
                        </button>
                        <button type="button" @click="formatDoc('italic')" title="مائل">
                            <i class="fas fa-italic"></i>
                        </button>
                        <button type="button" @click="formatDoc('underline')" title="تحته خط">
                            <i class="fas fa-underline"></i>
                        </button>
                        <div class="border-r border-white/20 h-6 mx-2"></div>
                        <button type="button" @click="formatDoc('justifyRight')" title="محاذاة يمين">
                            <i class="fas fa-align-right"></i>
                        </button>
                        <button type="button" @click="formatDoc('justifyCenter')" title="محاذاة وسط">
                            <i class="fas fa-align-center"></i>
                        </button>
                        <button type="button" @click="formatDoc('justifyLeft')" title="محاذاة يسار">
                            <i class="fas fa-align-left"></i>
                        </button>
                        <div class="border-r border-white/20 h-6 mx-2"></div>
                        <button type="button" @click="formatDoc('insertUnorderedList')" title="قائمة نقطية">
                            <i class="fas fa-list-ul"></i>
                        </button>
                        <button type="button" @click="formatDoc('insertOrderedList')" title="قائمة مرقمة">
                            <i class="fas fa-list-ol"></i>
                        </button>
                        <div class="border-r border-white/20 h-6 mx-2"></div>
                        <div class="variables-dropdown">
                            <button type="button" @click="toggleVariablesMenu()" title="إدراج متغير">
                                <i class="fas fa-code ml-1"></i>
                                متغيرات
                            </button>
                            <div class="variables-menu" :class="{ 'show': showVariablesMenu }">
                                <button type="button" @click="insertVariable('@{{client_name}}')">اسم العميل</button>
                                <button type="button" @click="insertVariable('@{{project_name}}')">اسم المشروع</button>
                                <button type="button" @click="insertVariable('@{{service_name}}')">اسم الخدمة</button>
                                <button type="button" @click="insertVariable('@{{date}}')">التاريخ</button>
                                @if($type === 'quotation')
                                <button type="button" @click="insertVariable('@{{total_price}}')">السعر الإجمالي</button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Editor Content -->
                    <div 
                        id="editor-content"
                        contenteditable="true"
                        class="editor-content"
                        x-ref="editor"
                        @input="updateContent()"
                    ></div>
                    <textarea 
                        name="content" 
                        x-model="content"
                        class="hidden"
                        required
                    ></textarea>
                    @error('content')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Document Info -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <h2 class="text-lg font-bold text-white mb-4">معلومات المستند</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <label class="text-gray-400">رقم المستند</label>
                            <p class="text-white font-semibold">سيتم التوليد تلقائياً</p>
                        </div>
                        <div>
                            <label class="text-gray-400">الحالة</label>
                            <p class="text-white font-semibold">
                                <span class="bg-gray-500/20 text-gray-400 px-2 py-0.5 rounded text-xs">مسودة</span>
                            </p>
                        </div>
                        <div>
                            <label class="text-gray-400">أنشئ بواسطة</label>
                            <p class="text-white font-semibold">{{ auth()->user()->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <h2 class="text-lg font-bold text-white mb-4">الإجراءات</h2>
                    <div class="space-y-2">
                        <button 
                            type="submit" 
                            :disabled="loading"
                            :class="loading ? 'opacity-50 cursor-not-allowed' : ''"
                            class="w-full px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200"
                        >
                            <i class="fas fa-save ml-2"></i>
                            <span x-show="!loading">حفظ مسودة</span>
                            <span x-show="loading">جاري الحفظ...</span>
                        </button>
                        <button 
                            type="button"
                            @click="previewPdf()"
                            class="w-full px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200"
                        >
                            <i class="fas fa-file-pdf ml-2"></i>
                            معاينة PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function documentEditor() {
    return {
        templateId: '{{ old("template_id", "") }}',
        projectId: '{{ old("project_id", $projectId ?? "") }}',
        clientId: '{{ old("client_id", $clientId ?? "") }}',
        title: '{{ old("title", "") }}',
        content: '{{ old("content", "") }}',
        loading: false,
        showVariablesMenu: false,
        templates: @json($templates->keyBy('id')),

        init() {
            // Load template content if exists
            if (this.content) {
                this.$refs.editor.innerHTML = this.content;
            }
        },

        loadTemplate() {
            if (this.templateId && this.templates[this.templateId]) {
                const template = this.templates[this.templateId];
                if (template.content) {
                    this.$refs.editor.innerHTML = template.content;
                    this.updateContent();
                }
            }
        },

        updateClientFromProject() {
            // This will be handled by backend or AJAX if needed
            // For now, we'll let the backend handle it
        },

        formatDoc(cmd, value = null) {
            document.execCommand(cmd, false, value);
            this.$refs.editor.focus();
        },

        toggleVariablesMenu() {
            this.showVariablesMenu = !this.showVariablesMenu;
        },

        insertVariable(variable) {
            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                range.deleteContents();
                const textNode = document.createTextNode(variable);
                range.insertNode(textNode);
                range.setStartAfter(textNode);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);
            } else {
                this.$refs.editor.innerHTML += variable;
            }
            this.updateContent();
            this.showVariablesMenu = false;
        },

        updateContent() {
            this.content = this.$refs.editor.innerHTML;
        },

        previewPdf() {
            // Save content first
            this.updateContent();
            
            // Note: Preview will be available after document is saved
            alert('يرجى حفظ المستند أولاً لمعاينة PDF');
        },

        submitForm(event) {
            this.updateContent();
            
            if (!this.title || this.title.trim() === '') {
                alert('يرجى إدخال عنوان المستند');
                return false;
            }
            
            if (!this.content || this.content.trim() === '') {
                alert('يرجى إدخال محتوى المستند');
                return false;
            }
            
            this.loading = true;
            const form = event.target;
            form.submit();
        }
    }
}
</script>
@endpush
@endsection
