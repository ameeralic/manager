<template>
    <div class="min-h-screen flex flex-col">
        <nav class="bg-indigo-600 text-white shadow">
            <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
                <Link href="/" class="text-lg font-semibold tracking-tight hover:opacity-90">
                    Manager
                </Link>
                <div class="flex items-center gap-6 text-sm font-medium">
                    <Link :href="route('home')" :class="navClass('home')">Home</Link>
                    <Link :href="route('about')" :class="navClass('about')">About</Link>
                    <Link :href="route('dashboard')" :class="navClass('dashboard')">Dashboard</Link>
                </div>
            </div>
        </nav>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-gray-200 py-4 text-center text-xs text-gray-400">
            Manager &copy; {{ new Date().getFullYear() }}
        </footer>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

function navClass(routeName) {
    const isActive = page.url === route(routeName) || page.component === routeName.charAt(0).toUpperCase() + routeName.slice(1);
    return isActive
        ? 'text-white underline underline-offset-4'
        : 'text-indigo-200 hover:text-white transition-colors';
}
</script>
