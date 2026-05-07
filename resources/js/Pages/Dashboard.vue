<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-6 py-12">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-notes-text tracking-tight">
                        Good {{ greeting }}, {{ auth.user?.name }}
                    </h1>
                    <p class="text-sm text-notes-secondary mt-0.5">{{ formattedDate }}</p>
                </div>
            </div>

            <!-- Empty state -->
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-16 h-16 rounded-2xl bg-notes-yellow/15 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-notes-yellow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-notes-text font-medium mb-1">Nothing here yet</p>
                <p class="text-sm text-notes-secondary">Your home manager is ready to go.</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const auth = page.props.auth;

const greeting = computed(() => {
    const h = new Date().getHours();
    if (h < 12) return 'morning';
    if (h < 18) return 'afternoon';
    return 'evening';
});

const formattedDate = computed(() => {
    return new Date().toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
    });
});
</script>
