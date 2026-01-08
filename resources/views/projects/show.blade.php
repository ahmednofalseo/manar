@extends('layouts.dashboard')

@section('title', __('Project Details') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Project Details'))

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
<div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 flex-1">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm sm:text-base">{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 flex-1">
            <i class="fas fa-exclamation-circle"></i>
            <span class="text-sm sm:text-base">{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('warning'))
<div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-yellow-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 flex-1">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="text-sm sm:text-base">{{ session('warning') }}</span>
        </div>
        <button @click="show = false" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<div x-data="projectTabs()">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $project->name }}</h1>
            <p class="text-gray-400 text-sm">{{ $project->project_number ?? 'غير محدد' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.edit', $project->id) }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-edit {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Edit') }}
            </a>
            <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="glass-card rounded-xl md:rounded-2xl mb-4 md:mb-6">
        <div class="border-b border-white/10 flex flex-wrap overflow-x-auto">
            <button 
                @click="activeTab = 'overview'"
                :class="activeTab === 'overview' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-gauge"></i>
                {{ __('Overview') }}
            </button>
            <button 
                @click="activeTab = 'stages'"
                :class="activeTab === 'stages' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-diagram-project"></i>
                {{ __('Stages') }}
                @if($stagesCount > 0)
                <span class="bg-primary-400/20 text-primary-400 px-2 py-0.5 rounded text-xs">{{ $stagesCount }}</span>
                @endif
            </button>
            <button 
                @click="activeTab = 'tasks'"
                :class="activeTab === 'tasks' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-list-check"></i>
                {{ __('Tasks') }}
                @if($tasksCount > 0)
                <span class="bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded text-xs">{{ $tasksCount }}</span>
                @endif
            </button>
            <button 
                @click="activeTab = 'attachments'"
                :class="activeTab === 'attachments' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-paperclip"></i>
                {{ __('Attachments') }}
                @if($attachmentsCount > 0)
                <span class="bg-green-500/20 text-green-400 px-2 py-0.5 rounded text-xs">{{ $attachmentsCount }}</span>
                @endif
            </button>
            @if(\App\Helpers\PermissionHelper::hasPermission('financials.view') || \App\Helpers\PermissionHelper::hasPermission('financials.manage'))
            <button 
                @click="activeTab = 'financials'"
                :class="activeTab === 'financials' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-file-invoice-dollar"></i>
                المالية
            </button>
            @endif
            <button 
                @click="activeTab = 'thirdparty'"
                :class="activeTab === 'thirdparty' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-handshake"></i>
                الطرف الثالث
            </button>
            <button 
                @click="activeTab = 'activity'"
                :class="activeTab === 'activity' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-clock-rotate-left"></i>
                السجل
            </button>
            @if($project->status !== 'مكتمل')
            <button 
                @click="activeTab = 'chat'"
                :class="activeTab === 'chat' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-comments"></i>
                {{ __('Chat') }}
            </button>
            @endif
        </div>
    </div>

    <!-- Tab Content -->
    <!-- Overview Tab -->
    <div x-show="activeTab === 'overview'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.overview')
    </div>

    <!-- Stages Tab -->
    <div x-show="activeTab === 'stages'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.stages')
    </div>

    <!-- Tasks Tab -->
    <div x-show="activeTab === 'tasks'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.tasks')
    </div>

    <!-- Attachments Tab -->
    <div x-show="activeTab === 'attachments'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.attachments')
    </div>

    <!-- Financials Tab -->
    @if(\App\Helpers\PermissionHelper::hasPermission('financials.view') || \App\Helpers\PermissionHelper::hasPermission('financials.manage'))
    <div x-show="activeTab === 'financials'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.financials')
    </div>
    @endif

    <!-- Third Party Tab -->
    <div x-show="activeTab === 'thirdparty'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.thirdparty')
    </div>

    <!-- Activity Tab -->
    <div x-show="activeTab === 'activity'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.activity')
    </div>

    <!-- Chat Tab -->
    @if($project->status !== 'مكتمل')
    <div x-show="activeTab === 'chat'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.chat', ['currentUserId' => auth()->user()->id])
    </div>
    @endif
</div>

@push('scripts')
<script>
    function projectTabs() {
        return {
            activeTab: 'overview'
        }
    }
</script>
@endpush

@if($project->status !== 'مكتمل')
@push('scripts')
<script>
    function projectChat(projectId, currentUserId) {
        return {
            projectId: projectId,
            currentUserId: currentUserId,
            errorMessage: {!! json_encode(__('Error sending message')) !!},
            fileSizeMessage: {!! json_encode(__('File size must be less than 10MB')) !!},
            justNow: {!! json_encode(__('Just now')) !!},
            minutesAgo: {!! json_encode(__('minutes ago')) !!},
            hoursAgo: {!! json_encode(__('hours ago')) !!},
            locale: {!! json_encode(app()->getLocale()) !!},
            conversation: null,
            messages: [],
            participants: [],
            newMessage: '',
            attachment: null,
            attachmentPreview: null,
            loading: true,
            sending: false,
            lastMessageId: null,
            pollingInterval: null,

            async init() {
                await this.loadConversation();
                this.startPolling();
                
                // Auto scroll to bottom
                this.$watch('messages', () => {
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                });
            },

            async loadConversation() {
                try {
                    this.loading = true;
                    const response = await fetch(`/chat/project/${this.projectId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.conversation = data.conversation;
                        this.messages = data.messages.data.reverse(); // Reverse to show oldest first
                        this.participants = data.participants || [];
                        this.lastMessageId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : null;
                    }
                } catch (error) {
                    console.error('Error loading conversation:', error);
                } finally {
                    this.loading = false;
                }
            },

            async sendMessage() {
                if (!this.newMessage.trim() && !this.attachment) return;
                if (this.sending) return;

                this.sending = true;

                try {
                    const formData = new FormData();
                    formData.append('message', this.newMessage);
                    if (this.attachment) {
                        formData.append('attachment', this.attachment);
                    }

                    const response = await fetch(`/chat/conversation/${this.conversation.id}/send`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.messages.push(data.message);
                        this.newMessage = '';
                        this.clearAttachment();
                        this.scrollToBottom();
                        this.lastMessageId = data.message.id;
                    } else {
                        alert(data.message || this.errorMessage);
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    alert(this.errorMessage);
                } finally {
                    this.sending = false;
                }
            },

            handleAttachment(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 10 * 1024 * 1024) {
                        alert(this.fileSizeMessage);
                        return;
                    }
                    this.attachment = file;
                    this.attachmentPreview = {
                        name: file.name,
                        size: file.size
                    };
                }
            },

            clearAttachment() {
                this.attachment = null;
                this.attachmentPreview = null;
                document.getElementById('chat-attachment').value = '';
            },

            startPolling() {
                // Poll for new messages every 3 seconds
                this.pollingInterval = setInterval(() => {
                    if (this.conversation && !this.conversation.is_closed) {
                        this.checkNewMessages();
                    }
                }, 3000);
            },

            async checkNewMessages() {
                try {
                    const response = await fetch(`/chat/conversation/${this.conversation.id}/messages?last_message_id=${this.lastMessageId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });

                    const data = await response.json();

                    if (data.success && data.messages.data.length > 0) {
                        // Add new messages
                        const newMessages = data.messages.data.filter(msg => 
                            !this.messages.find(m => m.id === msg.id)
                        );
                        
                        if (newMessages.length > 0) {
                            this.messages.push(...newMessages);
                            this.lastMessageId = newMessages[newMessages.length - 1].id;
                            this.scrollToBottom();
                        }
                    }

                    // Update conversation status
                    if (data.conversation) {
                        this.conversation = data.conversation;
                    }
                } catch (error) {
                    console.error('Error checking new messages:', error);
                }
            },

            scrollToBottom() {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            },

            getAvatarUrl(user) {
                // إذا كان هناك avatar_url (من accessor في Model)
                if (user.avatar_url) {
                    return user.avatar_url;
                }
                // إذا كان هناك avatar (مسار الملف)
                if (user.avatar) {
                    return '/storage/' + user.avatar;
                }
                // إذا لم يكن هناك صورة، استخدم ui-avatars
                return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=1db8f8&color=fff&size=48';
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;
                
                if (diff < 60000) { // Less than 1 minute
                    return this.justNow;
                } else if (diff < 3600000) { // Less than 1 hour
                    const minutes = Math.floor(diff / 60000);
                    return minutes + ' ' + this.minutesAgo;
                } else if (diff < 86400000) { // Less than 1 day
                    const hours = Math.floor(diff / 3600000);
                    return hours + ' ' + this.hoursAgo;
                } else {
                    return date.toLocaleDateString(this.locale, {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            },

            destroyed() {
                if (this.pollingInterval) {
                    clearInterval(this.pollingInterval);
                }
            }
        }
    }
</script>
@endpush
@endif

@endsection
