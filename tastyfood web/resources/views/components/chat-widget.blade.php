@auth
@php
$chatEnabled = App\Models\Setting::get('chat_enabled', '1') === '1';
@endphp

@if($chatEnabled)
<div id="chat-widget" x-data="chatWidget()" x-init="init()" class="fixed bottom-6 right-6 z-50">
    <!-- Connection Error Banner -->
    <div x-show="!isConnected && isOpen" 
         x-transition
         class="absolute bottom-full mb-2 left-0 right-0 bg-red-500 text-white text-sm px-4 py-2 rounded-lg shadow-lg"
         style="display: none;">
        Koneksi chat terputus. Mencoba menghubungkan kembali...
    </div>

    <!-- Chat Button -->
    <button 
        @click="toggleChat()" 
        x-show="!isOpen"
        class="bg-amber-500 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-110 relative">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <span x-show="unreadCount > 0" 
              x-text="unreadCount" 
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
        </span>
    </button>

    <!-- Chat Window -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="chat-window bg-white rounded-lg shadow-2xl w-[360px] max-w-[calc(100vw-2rem)] h-[500px] max-h-[calc(100vh-6rem)] flex flex-col overflow-hidden"
        style="display: none;">
        
        <!-- Header -->
        <div class="bg-amber-500 text-white p-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-sm">Chat dengan Admin</div>
                    <div class="text-xs opacity-90" x-show="adminTyping">Admin sedang mengetik...</div>
                    <div class="text-xs opacity-90" x-show="!adminTyping && isOnline">Online</div>
                    <div class="text-xs opacity-90" x-show="!adminTyping && !isOnline">Offline</div>
                </div>
            </div>
            <button @click="toggleChat()" class="hover:bg-white/10 rounded p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Messages -->
        <div 
            x-ref="messagesContainer"
            @scroll="handleScroll($event)"
            class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 min-h-0">
            
            <template x-if="loading">
                <div class="flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
                </div>
            </template>

            <template x-for="message in messages" :key="message.uuid || message.id">
                <div :class="message.sender_type === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="message.sender_type === 'user' 
                        ? 'bg-amber-500 text-white rounded-lg rounded-br-none' 
                        : message.message_type === 'system' 
                            ? 'bg-gray-200 text-gray-600 text-xs italic rounded-lg text-center'
                            : 'bg-white text-gray-900 rounded-lg rounded-bl-none shadow'"
                        class="px-4 py-2 max-w-[75%] break-words">
                        <div x-text="message.message"></div>
                        <div :class="message.sender_type === 'user' ? 'text-amber-100' : 'text-gray-400'" 
                             class="text-xs mt-1" 
                             x-text="formatTime(message.created_at)">
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="userTyping" class="flex justify-start">
                <div class="bg-white text-gray-500 rounded-lg rounded-bl-none shadow px-4 py-2">
                    <div class="flex gap-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input -->
        <div class="p-4 border-t border-gray-200 bg-white shrink-0">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input 
                    type="text" 
                    x-model="newMessage" 
                    @input="handleTyping()"
                    placeholder="Tulis pesan..."
                    :disabled="sending || !isConnected"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                />
                <button 
                    type="submit" 
                    :disabled="sending || !newMessage.trim() || !isConnected"
                    class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shrink-0">
                    <svg x-show="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <div x-show="sending" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openChat(orderId) {
    const el = document.getElementById('chat-widget');
    if (!el) return;
    
    if (el.__x && el.__x.$data) {
        el.__x.$data.orderId = orderId;
        el.__x.$data.isOpen = false;
        el.__x.$data.toggleChat();
    }
}

window.openChat = openChat;

function chatWidget() {
    return {
        isOpen: false,
        conversationId: null,
        orderId: null,
        messages: [],
        newMessage: '',
        loading: false,
        sending: false,
        adminTyping: false,
        userTyping: false,
        unreadCount: 0,
        isOnline: false,
        isConnected: true,
        channel: null,
        typingTimeout: null,
        pendingMessageIds: new Set(),

        async init() {
            this.setupEchoConnection();
            
            try {
                const response = await fetch('/api/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({})
                });
                const data = await response.json();
                if (data.success) {
                    this.conversationId = data.conversation.id;
                }
            } catch (error) {
                console.error('Failed to initialize chat:', error);
            }
        },

        setupEchoConnection() {
            if (!window.Echo) return;

            window.Echo.connector.socket.on('connect', () => {
                this.isConnected = true;
                this.setupPresence();
                // Re-subscribe to conversation channel if open
                if (this.isOpen && this.conversationId) {
                    this.setupEcho();
                }
            });

            window.Echo.connector.socket.on('disconnect', () => {
                this.isConnected = false;
            });

            window.Echo.connector.socket.on('reconnecting', () => {
                this.isConnected = false;
            });
        },

        setupPresence() {
            if (!window.Echo) return;

            window.Echo.join('chat-online')
                .here((users) => {
                    this.isOnline = users.some(u => u.type === 'admin');
                })
                .joining((user) => {
                    if (user.type === 'admin') this.isOnline = true;
                })
                .leaving((user) => {
                    if (user.type === 'admin') {
                        // Recheck if any admin still online
                        // Presence doesn't give full list on leave, so we can't reliably update here
                    }
                });
        },

        async loadMessages() {
            if (!this.conversationId) {
                await this.initConversation();
            }

            if (!this.conversationId) return;

            this.loading = true;
            try {
                const response = await fetch(`/api/chat/${this.conversationId}/messages`);
                const data = await response.json();
                if (data.success) {
                    this.messages = data.messages;
                    this.pendingMessageIds.clear();
                    this.$nextTick(() => this.scrollToBottom());
                    await this.markAsRead();
                    this.setupEcho();
                }
            } catch (error) {
                console.error('Failed to load messages:', error);
            } finally {
                this.loading = false;
            }
        },

        async initConversation() {
            try {
                const body = {};
                if (this.orderId) body.order_id = this.orderId;
                
                const response = await fetch('/api/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(body)
                });
                const data = await response.json();
                if (data.success) {
                    this.conversationId = data.conversation.id;
                }
            } catch (error) {
                console.error('Failed to create conversation:', error);
            }
        },

        setupEcho() {
            if (!this.conversationId || !window.Echo) return;

            // Leave previous channel if exists
            if (this.channel) {
                window.Echo.leave(`chat.${this.conversationId}`);
                this.channel = null;
            }

            this.channel = window.Echo.private(`chat.${this.conversationId}`)
                .listen('MessageSent', (e) => {
                    // Skip if this is a message we already added optimistically
                    if (this.pendingMessageIds.has(e.uuid)) {
                        // Update the optimistic message with server data
                        const idx = this.messages.findIndex(m => m.uuid === e.uuid);
                        if (idx !== -1) {
                            this.messages[idx] = { ...this.messages[idx], ...e, id: e.id || this.messages[idx].id };
                        }
                        this.pendingMessageIds.delete(e.uuid);
                        this.scrollToBottom();
                        return;
                    }
                    
                    // Dedup: check if message with same uuid already exists
                    const exists = this.messages.some(m => m.uuid === e.uuid);
                    if (!exists) {
                        this.messages.push(e);
                        this.scrollToBottom();
                    }
                    
                    if (this.isOpen && e.sender_type === 'admin') {
                        this.markAsRead();
                    } else if (!this.isOpen && e.sender_type === 'admin') {
                        this.unreadCount++;
                    }
                })
                .listen('MessageRead', (e) => {
                    if (e.reader_type === 'admin') {
                        this.messages.forEach(msg => {
                            if (msg.sender_type === 'user') msg.is_read = true;
                        });
                    }
                })
                .listen('UserTyping', (e) => {
                    if (e.user_type === 'admin') {
                        this.adminTyping = e.is_typing;
                    }
                });
        },

        async toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                await this.loadMessages();
                this.unreadCount = 0;
            } else {
                // Cleanup channel when closing
                if (this.channel) {
                    window.Echo.leave(`chat.${this.conversationId}`);
                    this.channel = null;
                }
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.sending || !this.conversationId) return;
            
            this.sending = true;
            const messageText = this.newMessage.trim();
            this.newMessage = '';

            // Generate optimistic UUID
            const optimisticUuid = crypto.randomUUID ? crypto.randomUUID() : 'temp-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            this.pendingMessageIds.add(optimisticUuid);

            // Optimistically add message to UI immediately
            const optimisticMessage = {
                uuid: optimisticUuid,
                conversation_id: this.conversationId,
                sender_id: null,
                sender_type: 'user',
                message: messageText,
                message_type: 'text',
                is_read: false,
                created_at: new Date().toISOString(),
                _optimistic: true
            };
            this.messages.push(optimisticMessage);
            this.scrollToBottom();

            try {
                const response = await fetch(`/api/chat/${this.conversationId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: messageText })
                });
                const data = await response.json();
                if (data.success) {
                    // Replace optimistic message with real one
                    const idx = this.messages.findIndex(m => m.uuid === optimisticUuid);
                    if (idx !== -1) {
                        this.messages[idx] = data.message;
                    }
                    this.pendingMessageIds.delete(optimisticUuid);
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Failed to send message:', error);
                // Mark optimistic message as failed
                const idx = this.messages.findIndex(m => m.uuid === optimisticUuid);
                if (idx !== -1) {
                    this.messages[idx]._failed = true;
                }
                this.newMessage = messageText;
            } finally {
                this.sending = false;
            }
        },

        async markAsRead() {
            if (!this.conversationId) return;
            try {
                await fetch(`/api/chat/${this.conversationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            } catch (error) {
                console.error('Failed to mark as read:', error);
            }
        },

        handleTyping() {
            if (!this.conversationId) return;
            
            if (!this.userTyping) {
                this.userTyping = true;
                this.sendTypingStatus(true);
            }

            clearTimeout(this.typingTimeout);
            this.typingTimeout = setTimeout(() => {
                this.userTyping = false;
                this.sendTypingStatus(false);
            }, 2000);
        },

        async sendTypingStatus(isTyping) {
            if (!this.conversationId) return;
            try {
                await fetch(`/api/chat/${this.conversationId}/typing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ is_typing: isTyping })
                });
            } catch (error) {
                console.error('Failed to send typing status:', error);
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.messagesContainer) {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                }
            });
        },

        handleScroll(event) {
            const el = event.target;
            if (el.scrollTop < 50 && !this.loading) {
                // TODO: implement pagination / load older messages
            }
        },

        formatTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
    };
}
</script>

<style>
@media (max-width: 768px) {
    #chat-widget .chat-window {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        max-width: none;
        max-height: none;
        border-radius: 0;
    }
}
</style>
@endif
@endauth

<!-- Mobile: Stack buttons vertically on small screens -->
<style>
@media (max-width: 640px) {
    #chat-widget {
        right: 1rem !important;
        bottom: 1rem !important;
    }
    #floating-checkout-btn {
        right: 1rem !important;
        bottom: 5rem !important; /* Stack above chat */
    }
}
</style>
