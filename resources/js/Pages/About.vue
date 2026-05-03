<template>
    <AppLayout>
        <div class="max-w-3xl mx-auto px-4 py-16">
            <h1 class="text-3xl font-bold mb-2">About</h1>
            <p class="text-gray-500 mb-10">What powers this application.</p>

            <div class="space-y-6">
                <div v-for="item in stack" :key="item.name" class="flex items-start gap-4 bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <span class="text-3xl">{{ item.icon }}</span>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h2 class="font-semibold text-gray-900">{{ item.name }}</h2>
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ item.version }}</span>
                        </div>
                        <p class="text-sm text-gray-500">{{ item.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

const stack = [
    { icon: '🐘', name: 'Laravel', version: 'v13', description: 'The PHP framework for web artisans — routing, middleware, Eloquent ORM, and more.' },
    { icon: '🔗', name: 'Inertia.js', version: 'v2', description: 'The modern monolith approach: server-driven routing, SPA-like navigation, no separate API.' },
    { icon: '💚', name: 'Vue 3', version: 'v3', description: 'Progressive JavaScript framework with Composition API and first-class TypeScript support.' },
    { icon: '💨', name: 'Tailwind CSS', version: 'v4', description: 'A utility-first CSS framework with a new CSS-native configuration model.' },
    { icon: '⚡', name: 'Vite', version: 'v6', description: 'Lightning-fast HMR and build tooling, with the official Laravel Vite plugin.' },
];
</script>
