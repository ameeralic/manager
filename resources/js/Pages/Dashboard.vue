<template>
    <AppLayout>
        <div class="max-w-6xl mx-auto px-4 py-10">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold">Dashboard</h1>
                    <p class="text-gray-500 text-sm mt-0.5">An overview of your application.</p>
                </div>
                <button
                    @click="refreshCount++"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors"
                >
                    Refresh ({{ refreshCount }})
                </button>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
                <div v-for="stat in stats" :key="stat.label" class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ stat.label }}</p>
                    <p class="text-3xl font-bold mt-1 text-indigo-600">{{ stat.value }}</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 font-semibold text-sm">Recent Activity</div>
                <ul class="divide-y divide-gray-100">
                    <li v-for="event in events" :key="event.id" class="flex items-center gap-3 px-5 py-3.5">
                        <span :class="['inline-block w-2 h-2 rounded-full flex-shrink-0', event.color]"></span>
                        <span class="text-sm text-gray-700 flex-1">{{ event.message }}</span>
                        <span class="text-xs text-gray-400">{{ event.time }}</span>
                    </li>
                </ul>
            </div>

            <div class="mt-6 bg-indigo-50 border border-indigo-200 rounded-xl p-5 text-sm text-indigo-800">
                <strong>SPA confirmed:</strong> Click the nav links above — the page does not reload. This counter stays at
                <strong>{{ refreshCount }}</strong> across navigations because Inertia preserves the component state while the layout persists.
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const refreshCount = ref(0);

const stats = [
    { label: 'Users', value: '128' },
    { label: 'Sessions', value: '3.4k' },
    { label: 'Requests', value: '92k' },
    { label: 'Errors', value: '0' },
];

const events = [
    { id: 1, message: 'Inertia page visit to /dashboard', time: 'just now', color: 'bg-green-400' },
    { id: 2, message: 'Vue component mounted successfully', time: '1s ago', color: 'bg-indigo-400' },
    { id: 3, message: 'Tailwind styles compiled via Vite', time: '2s ago', color: 'bg-purple-400' },
    { id: 4, message: 'Laravel handled the initial request', time: '3s ago', color: 'bg-blue-400' },
    { id: 5, message: 'Application booted cleanly', time: '4s ago', color: 'bg-gray-400' },
];
</script>
