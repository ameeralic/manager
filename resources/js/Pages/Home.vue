<template>
    <AppLayout>
        <div class="max-w-6xl mx-auto px-4 py-16">
            <div class="text-center">
                <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-semibold px-3 py-1 rounded-full mb-4 uppercase tracking-wide">
                    VILT Stack
                </span>
                <h1 class="text-4xl font-bold mb-4">Welcome to Manager</h1>
                <p class="text-gray-500 text-lg max-w-xl mx-auto mb-8">
                    This is a Vue 3 + Inertia.js + Laravel + Tailwind CSS SPA. Navigation happens without full page reloads.
                </p>
                <div class="flex justify-center gap-4">
                    <Link :href="route('dashboard')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2.5 rounded-lg transition-colors">
                        Go to Dashboard
                    </Link>
                    <Link :href="route('about')" class="border border-gray-300 hover:border-indigo-400 text-gray-700 font-medium px-6 py-2.5 rounded-lg transition-colors">
                        Learn More
                    </Link>
                </div>
            </div>

            <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div v-for="card in cards" :key="card.title" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="text-2xl mb-3">{{ card.icon }}</div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ card.title }}</h3>
                    <p class="text-sm text-gray-500">{{ card.description }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const cards = [
    {
        icon: '⚡',
        title: 'Inertia.js',
        description: 'Client-side navigation without building a separate API. The server stays in control of routing.',
    },
    {
        icon: '🎨',
        title: 'Vue 3',
        description: 'Composition API, reactive state, and single-file components with <script setup>.',
    },
    {
        icon: '💨',
        title: 'Tailwind CSS v4',
        description: 'Utility-first CSS with zero-config content detection and CSS-based theming.',
    },
];
</script>
