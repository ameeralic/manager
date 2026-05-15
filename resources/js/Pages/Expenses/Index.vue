<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-6 py-10">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Expenses</h1>
                <Link :href="route('expenses.create')" class="text-sm font-medium bg-notes-yellow text-amber-900 px-4 py-2 rounded-lg hover:opacity-90 transition-opacity">
                    Add Expense
                </Link>
            </div>

            <div v-if="$page.props.flash?.success" class="mb-4 px-4 py-2.5 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm">
                {{ $page.props.flash.success }}
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl border border-black/8 dark:border-white/8 py-16 text-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">Expense list coming soon.</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    expenses: Array,
});
</script>
