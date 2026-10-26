@extends('layouts.admin')

@section('title', 'Chat - Admin Padang Rice')
@section('header', 'Chat')

@section('content')
<div class="flex h-[calc(100vh-120px)] bg-white rounded-lg shadow overflow-hidden" x-data="adminChat()" x-init="init()">
    <!-- Sidebar -->
    <div class="w-full md:w-80 border-r border-gray-200 flex flex-col" :class="selectedConversation ? 'hidden md:flex' : ''">
        <!-- Search -->
        <div class="p-4 border-b border-gray-200">
            <input type="text" x-model="searchQuery" @input.debounce.300ms="search()" placeholder="Cari customer..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm" />
        </div>

        <!-- Conversations -->
        <div class="flex-1 overflow-y-auto">
            <template x-if="loadingConversations">
                <div class="flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
                </div>
            </template>

            <template x-for="conv in conversations" :key="conv.id">
                <div @click="selectConversation(conv.id)" 
                     :class="selectedConversation === conv.id ? 'bg-amber-50 border-l-4 border-amber-500' : 'hover:bg-gray-50 border-l-4 border-transparent'"
                     class="p-4 cursor-pointer border-b border-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full" :class="conv.user_online ? 'bg-green-500' : 'bg-gray-300'"></div>
                                <div class="font-medium text-sm truncate" x-text="conv.user?.name || 'Customer'"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1 truncate" x-text="conv.latest_message?.message || 'Belum ada pesan'"></div>
                        </div>
                        <div class="flex flex-col items-end ml-2">
                            <div class="text-xs text-gray-400" x-text="formatTime(conv.last_message_at)"></div>
                            <div x-show="conv.unread_count > 0" 
                                 x-text="conv.unread_count"
                                 class="mt-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="!loadingConversations && conversations.length === 0" class="p-4 text-center text-gray-400 text-sm">
                Belum ada percakapan
            </div>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col min-w-0" :class="!selectedConversation ? 'hidden md:flex' : ''">
        <!-- Connection Banner -->
        <div x-show="!isConnected" class="bg-red-500 text-white text-sm px-4 py-2 text-center" style="display: none;">
            Koneksi chat terputus. Mencoba menghubungkan kembali...
        </div>

        <!-- Chat Header -->
        <div x-show="selectedConversation" class="p-4 border-b border-gray-200 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <button @click="selectedConversation = null; activeConversation = null; messages = []" class="md:hidden text-gray-500 hover:text-gray-700 mr-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-sm" x-text="activeConversation?.user?.name || 'Customer'"></div>
                    <div class="text-xs" :class="activeConversation?.user_online ? 'text-green-600' : 'text-gray-400'" x-text="activeConversation?.user_online ? 'Online' : 'Offline'"></div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span x-show="userTyping" class="text-xs text-gray-500 italic">Customer sedang mengetik...</span>
                <button @click="closeConversation()" class="text-red-500 hover:text-red-700 text-sm px-3 py-1 rounded border border-red-200 hover:bg-red-50">
                    Tutup
                </button>
            </div>
        </div>

        <!-- Messages -->
        <div x-show="selectedConversation" x-ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 min-h-0">
            <template x-if="loadingMessages">
                <div class="flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
                </div>
            </template>

            <template x-for="message in messages" :key="message.uuid || message.id">
                <div :class="message.sender_type === 'admin' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="message.sender_type === 'admin' 
                        ? 'bg-amber-500 text-white rounded-lg rounded-br-none' 
                        : message.message_type === 'system' 
                            ? 'bg-gray-200 text-gray-600 text-xs italic rounded-lg text-center'
                            : 'bg-white text-gray-900 rounded-lg rounded-bl-none shadow'"
                        class="px-4 py-2 max-w-[75%] break-words">
                        <div x-text="message.message"></div>
                        <div :class="message.sender_type === 'admin' ? 'text-amber-100' : 'text-gray-400'" 
                             class="text-xs mt-1" 
                             x-text="formatTime(message.created_at)">
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="!selectedConversation" class="flex-1 flex items-center justify-center bg-gray-50">
            <div class="text-center text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-sm">Pilih percakapan untuk memulai chat</p>
            </div>
        </div>

        <!-- Input -->
        <div x-show="selectedConversation" class="p-4 border-t border-gray-200 bg-white shrink-0">
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
function adminChat() {
    return {
        conversations: [],
        selectedConversation: null,
        activeConversation: null,
        messages: [],
        newMessage: '',
        searchQuery: '',
        loadingConversations: false,
        loadingMessages: false,
        sending: false,
        userTyping: false,
        isConnected: true,
        channel: null,
        adminChannel: null,
        typingTimeout: null,
        channelConversationId: null,
        pendingMessageIds: new Set(),

        async init() {
            await this.loadConversations();
            this.setupEchoConnection();
            this.setupAdminChannel();
            this.loadTotalUnread();
            
            // Poll unread count every 30s as fallback
            setInterval(() => this.loadTotalUnread(), 30000);
        },

        setupEchoConnection() {
            if (!window.Echo) return;

            window.Echo.connector.socket.on('connect', () => {
                this.isConnected = true;
                // Resubscribe to channels
                this.setupAdminChannel();
                if (this.selectedConversation) {
                    this.setupConversationChannel(this.selectedConversation);
                }
            });

            window.Echo.connector.socket.on('disconnect', () => {
                this.isConnected = false;
            });

            window.Echo.connector.socket.on('reconnecting', () => {
                this.isConnected = false;
            });
        },

        setupAdminChannel() {
            if (!window.Echo) return;

            // Subscribe to admin-chat channel for all incoming messages
            if (this.adminChannel) {
                window.Echo.leave('admin-chat');
            }

            this.adminChannel = window.Echo.private('admin-chat')
                .listen('MessageSent', (e) => {
                    // Update conversation list
                    this.loadConversations();
                    this.loadTotalUnread();

                    // If message is for currently selected conversation, add it
                    if (e.conversation_id === this.selectedConversation && e.sender_type === 'user') {
                        // Check dedup
                        const exists = this.messages.some(m => m.uuid === e.uuid);
                        if (!exists) {
                            this.messages.push(e);
                            this.scrollToBottom();
                            this.markAsRead(e.conversation_id);
                        }
                    }
                });
        },

        setupConversationChannel(conversationId) {
            if (!window.Echo || !conversationId) return;

            // Leave previous conversation channel
            if (this.channel && this.channelConversationId !== conversationId) {
                window.Echo.leave(`chat.${this.channelConversationId}`);
                this.channel = null;
            }

            this.channelConversationId = conversationId;
            this.channel = window.Echo.private(`chat.${conversationId}`)
                .listen('MessageSent', (e) => {
                    // Skip if from pending optimistic
                    if (this.pendingMessageIds.has(e.uuid)) {
                        const idx = this.messages.findIndex(m => m.uuid === e.uuid);
                        if (idx !== -1) {
                            this.messages[idx] = { ...this.messages[idx], ...e };
                        }
                        this.pendingMessageIds.delete(e.uuid);
                        this.scrollToBottom();
                        return;
                    }

                    const exists = this.messages.some(m => m.uuid === e.uuid);
                    if (!exists) {
                        this.messages.push(e);
                        this.scrollToBottom();
                    }
                    
                    if (e.sender_type === 'user') {
                        this.markAsRead(conversationId);
                    }
                })
                .listen('MessageRead', (e) => {
                    if (e.reader_type === 'user') {
                        this.messages.forEach(msg => {
                            if (msg.sender_type === 'admin') msg.is_read = true;
                        });
                    }
                })
                .listen('UserTyping', (e) => {
                    if (e.user_type === 'user') {
                        this.userTyping = e.is_typing;
                    }
                });
        },

        async loadConversations() {
            this.loadingConversations = true;
            try {
                const res = await fetch('/admin/chat', {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.conversations = data.conversations;
                }
            } catch (error) {
                console.error('Failed to load conversations:', error);
            } finally {
                this.loadingConversations = false;
            }
        },

        async selectConversation(conversationId) {
            this.selectedConversation = conversationId;
            this.loadingMessages = true;

            try {
                const res = await fetch(`/admin/chat/${conversationId}`, {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.activeConversation = data.conversation;
                    this.messages = data.messages;
                    this.pendingMessageIds.clear();
                    this.$nextTick(() => this.scrollToBottom());
                    await this.markAsRead(conversationId);
                    this.setupConversationChannel(conversationId);
                }
            } catch (error) {
                console.error('Failed to load conversation:', error);
            } finally {
                this.loadingMessages = false;
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.sending || !this.selectedConversation) return;
            
            this.sending = true;
            const messageText = this.newMessage.trim();
            this.newMessage = '';

            // Optimistic UI
            const optimisticUuid = crypto.randomUUID ? crypto.randomUUID() : 'temp-' + Date.now();
            this.pendingMessageIds.add(optimisticUuid);

            const optimisticMessage = {
                uuid: optimisticUuid,
                conversation_id: this.selectedConversation,
                sender_type: 'admin',
                message: messageText,
                message_type: 'text',
                is_read: false,
                created_at: new Date().toISOString(),
                _optimistic: true
            };
            this.messages.push(optimisticMessage);
            this.scrollToBottom();

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch(`/admin/chat/${this.selectedConversation}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ message: messageText })
                });
                const data = await response.json();
                if (data.success) {
                    const idx = this.messages.findIndex(m => m.uuid === optimisticUuid);
                    if (idx !== -1) {
                        this.messages[idx] = data.message;
                    }
                    this.pendingMessageIds.delete(optimisticUuid);
                    this.scrollToBottom();
                    this.loadConversations();
                }
            } catch (error) {
                console.error('Failed to send message:', error);
                const idx = this.messages.findIndex(m => m.uuid === optimisticUuid);
                if (idx !== -1) {
                    this.messages[idx]._failed = true;
                }
                this.newMessage = messageText;
            } finally {
                this.sending = false;
            }
        },

        async markAsRead(conversationId) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                await fetch(`/admin/chat/${conversationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const conv = this.conversations.find(c => c.id === conversationId);
                if (conv) conv.unread_count = 0;
                this.loadTotalUnread();
            } catch (error) {
                console.error('Failed to mark as read:', error);
            }
        },

        handleTyping() {
            if (!this.selectedConversation) return;

            clearTimeout(this.typingTimeout);
            this.sendTypingStatus(true);
            this.typingTimeout = setTimeout(() => {
                this.sendTypingStatus(false);
            }, 2000);
        },

        async sendTypingStatus(isTyping) {
            if (!this.selectedConversation) return;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                await fetch(`/admin/chat/${this.selectedConversation}/typing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ is_typing: isTyping })
                });
            } catch (error) {
                console.error('Failed to send typing status:', error);
            }
        },

        async search() {
            if (!this.searchQuery) {
                await this.loadConversations();
                return;
            }

            try {
                const res = await fetch(`/admin/chat-search?q=${encodeURIComponent(this.searchQuery)}`, {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.conversations = data.conversations;
                }
            } catch (error) {
                console.error('Failed to search:', error);
            }
        },

        async closeConversation() {
            if (!this.selectedConversation) return;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                await fetch(`/admin/chat/${this.selectedConversation}/close`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                this.selectedConversation = null;
                this.activeConversation = null;
                this.messages = [];
                if (this.channel) {
                    window.Echo.leave(`chat.${this.channelConversationId}`);
                    this.channel = null;
                }
                await this.loadConversations();
            } catch (error) {
                console.error('Failed to close conversation:', error);
            }
        },

        async loadTotalUnread() {
            try {
                const res = await fetch('/admin/chat-unread', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                const badge = document.getElementById('chat-unread-badge');
                if (badge) {
                    if (data.unread > 0) {
                        badge.textContent = data.unread;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            } catch (e) {}
        },

        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.messagesContainer) {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                }
            });
        },

        formatTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
    };
}
</script>
@endsection
