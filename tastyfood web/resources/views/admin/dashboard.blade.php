@extends('layouts.admin')

@section('header', 'Dashboard')

@section('content')
<div x-data="dashboard()" x-init="init()">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Berita</p>
                    <p class="text-3xl font-bold text-gray-900" x-text="stats.total_news">{{ $stats['total_news'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Foto</p>
                    <p class="text-3xl font-bold text-gray-900" x-text="stats.total_galleries">{{ $stats['total_galleries'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pesan Masuk</p>
                    <p class="text-3xl font-bold text-gray-900" x-text="stats.unread_messages">{{ $stats['unread_messages'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h2 class="font-semibold">Berita Terbaru</h2>
                <div x-show="loading" class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-600"></div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <template x-for="news in recentNews" :key="news.id">
                        <div class="flex items-start gap-4">
                            <img :src="news.image || '/assets/ASET/nasipadang-hero.jpg'" 
                                 :alt="news.title" 
                                 class="w-16 h-16 object-cover rounded">
                            <div class="flex-1">
                                <h3 class="font-medium text-sm" x-text="news.title.substring(0, 50) + (news.title.length > 50 ? '...' : '')"></h3>
                                <p class="text-xs text-gray-500" x-text="news.created_at_human"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="recentNews.length === 0">
                        <p class="text-sm text-gray-500">Belum ada berita.</p>
                    </template>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h2 class="font-semibold">Pesan Terbaru</h2>
                <div x-show="loading" class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-600"></div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <template x-for="message in recentMessages" :key="message.id">
                        <div class="pb-4 border-b last:border-0">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-medium text-sm" x-text="message.name"></h3>
                                    <p class="text-xs text-gray-500" x-text="message.subject"></p>
                                </div>
                                <span x-show="!message.is_read" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Baru</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1" x-text="message.created_at_human"></p>
                        </div>
                    </template>
                    <template x-if="recentMessages.length === 0">
                        <p class="text-sm text-gray-500">Belum ada pesan.</p>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dashboard() {
    return {
        stats: {
            total_news: {{ $stats['total_news'] }},
            total_galleries: {{ $stats['total_galleries'] }},
            unread_messages: {{ $stats['unread_messages'] }}
        },
        recentNews: @json($recentNews),
        recentMessages: @json($recentMessages),
        loading: false,

        init() {
            this.startAutoRefresh();
        },

        startAutoRefresh() {
            setInterval(() => {
                this.refresh();
            }, 30000); // Refresh every 30 seconds
        },

        async refresh() {
            this.loading = true;
            try {
                const response = await fetch('{{ route('admin.dashboard') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.stats = data.stats;
                    this.recentNews = data.recentNews;
                    this.recentMessages = data.recentMessages;
                }
            } catch (e) {
                console.error('Failed to refresh dashboard:', e);
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endsection
