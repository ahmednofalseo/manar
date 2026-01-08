<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="projectChat({{ $project->id }}, {{ $currentUserId }})" x-init="init()">
    <div class="flex flex-col h-[600px] md:h-[700px]">
        <!-- Chat Header -->
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-white/10">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-comments text-primary-400"></i>
                    {{ __('Project Chat') }}
                </h2>
                <p class="text-gray-400 text-sm mt-1" x-show="participants.length > 0">
                    <span x-text="participants.length"></span> {{ __('participants') }}
                </p>
            </div>
            <div x-show="conversation && conversation.is_closed" class="px-3 py-1 bg-red-500/20 text-red-400 rounded-lg text-sm font-semibold">
                {{ __('Chat Closed') }}
            </div>
        </div>

        <!-- Messages Container -->
        <div class="flex-1 overflow-y-auto mb-4 px-2" id="messages-container" style="max-height: 500px;">
            <template x-if="loading">
                <div class="flex items-center justify-center py-8">
                    <i class="fas fa-spinner fa-spin text-primary-400 text-2xl"></i>
                </div>
            </template>

            <template x-if="!loading && messages.length === 0">
                <div class="text-center py-12 text-gray-400">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/5 mb-4">
                        <i class="fas fa-comments text-3xl opacity-50"></i>
                    </div>
                    <p class="text-base">{{ __('No messages yet. Start the conversation!') }}</p>
                </div>
            </template>

            <template x-for="message in messages" :key="message.id">
                <div class="flex mb-4" :class="message.user_id === currentUserId ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[80%] md:max-w-[65%] flex gap-3" :class="message.user_id === currentUserId ? 'flex-row-reverse' : 'flex-row'">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="relative">
                                <img :src="getAvatarUrl(message.user)" 
                                     :alt="message.user.name" 
                                     class="w-10 h-10 md:w-12 md:h-12 rounded-full border-2 shadow-lg object-cover"
                                     :class="message.user_id === currentUserId ? 'border-primary-400/50' : 'border-gray-600/50'"
                                     x-on:error="$event.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(message.user.name) + '&background=1db8f8&color=fff&size=48'">
                                <div class="absolute -bottom-1 -right-1 w-3 h-3 rounded-full border-2 border-gray-800"
                                     :class="message.user_id === currentUserId ? 'bg-primary-400' : 'bg-green-400'"></div>
                            </div>
                        </div>
                        
                        <!-- Message Content -->
                        <div class="flex flex-col gap-1" :class="message.user_id === currentUserId ? 'items-end' : 'items-start'">
                            <!-- User Name and Time -->
                            <div class="flex items-center gap-2 px-2" :class="message.user_id === currentUserId ? 'flex-row-reverse' : 'flex-row'">
                                <span class="text-gray-300 font-medium text-xs" x-text="message.user.name"></span>
                                <span class="text-gray-500 text-xs" x-text="formatDate(message.created_at)"></span>
                            </div>
                            
                            <!-- Message Bubble -->
                            <div :class="message.user_id === currentUserId 
                                ? 'bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-500/20 rounded-[20px_20px_4px_20px]' 
                                : 'bg-white/10 backdrop-blur-sm text-white border border-white/20 shadow-lg rounded-[20px_20px_20px_4px]'" 
                                class="px-4 py-3 max-w-full break-words relative">
                                
                                <!-- Message Text -->
                                <template x-if="message.message">
                                    <p class="text-sm leading-relaxed whitespace-pre-wrap mb-2 text-white" 
                                       x-text="message.message"></p>
                                </template>
                                
                                <!-- Attachment -->
                                <template x-if="message.attachment">
                                    <a :href="'/storage/' + message.attachment" target="_blank" 
                                       class="inline-flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all group mb-2"
                                       :class="message.user_id === currentUserId 
                                           ? 'bg-white/20 hover:bg-white/30 text-white' 
                                           : 'bg-white/10 hover:bg-white/20 text-white'">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                             :class="message.user_id === currentUserId 
                                                 ? 'bg-white/20' 
                                                 : 'bg-white/10'">
                                            <i class="fas fa-paperclip"></i>
                                        </div>
                                        <span class="flex-1 min-w-0 truncate font-medium" 
                                              x-text="message.attachment_name || message.attachment.split('/').pop()"></span>
                                        <i class="fas fa-external-link-alt text-xs opacity-60 group-hover:opacity-100 transition-opacity"></i>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Message Input -->
        <div x-show="conversation && !conversation.is_closed" class="border-t border-white/10 pt-4">
            <form @submit.prevent="sendMessage()" class="space-y-3">
                <!-- Attachment Preview -->
                <template x-if="attachmentPreview">
                    <div class="flex items-center gap-2 p-2 bg-white/5 rounded-lg">
                        <i class="fas fa-paperclip text-primary-400"></i>
                        <span class="text-white text-sm flex-1" x-text="attachmentPreview.name"></span>
                        <button type="button" @click="clearAttachment()" class="text-red-400 hover:text-red-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </template>

                <div class="flex gap-2">
                    <input 
                        type="file" 
                        id="chat-attachment" 
                        @change="handleAttachment($event)"
                        class="hidden"
                        accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx"
                    >
                    <label for="chat-attachment" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer transition-all">
                        <i class="fas fa-paperclip"></i>
                    </label>
                    
                    <textarea 
                        x-model="newMessage"
                        @keydown.enter.prevent="sendMessage()"
                        placeholder="{{ __('Type your message...') }}"
                        rows="2"
                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 resize-none"
                    ></textarea>
                    
                    <button 
                        type="submit"
                        :disabled="sending || !newMessage.trim()"
                        class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i class="fas fa-paper-plane" x-show="!sending"></i>
                        <i class="fas fa-spinner fa-spin" x-show="sending"></i>
                    </button>
                </div>
            </form>
        </div>

        <div x-show="conversation && conversation.is_closed" class="text-center py-8 text-gray-400 border-t border-white/10 pt-4">
            <i class="fas fa-lock text-2xl mb-2"></i>
            <p>{{ __('Chat is closed because the project is completed') }}</p>
        </div>
    </div>
</div>