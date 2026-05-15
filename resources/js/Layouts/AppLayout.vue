<template>
    <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-800">
        <!-- Top bar -->
        <header class="h-12 flex items-center px-5 border-b border-black/8 dark:border-white/8 sticky top-0 z-10 bg-white dark:bg-gray-900">
            <div class="flex-1 flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-notes-yellow flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-800" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">Home Manager</span>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">{{ currentTime }}</span>
                <span class="text-gray-300 dark:text-gray-600 text-xs">|</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ auth.user?.name }}</span>
                <button
                    @click="logout"
                    class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors px-2 py-1 rounded-md hover:bg-black/5 dark:hover:bg-white/5"
                >
                    Sign out
                </button>
                <button
                    @click="toggleTheme"
                    class="flex items-center justify-center w-7 h-7 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
                    :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <!-- Moon: shown in light mode, click to go dark -->
                    <svg v-if="!isDark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    <!-- Sun: shown in dark mode, click to go light -->
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/>
                        <line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/>
                        <line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Main content -->
        <main class="flex-1">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const page = usePage();
const auth = page.props.auth;

const currentTime = ref('');
const isDark = ref(false);
let intervalId = null;

const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

function formatDateTime(date) {
    const day = dayNames[date.getDay()];
    const month = monthNames[date.getMonth()];
    const d = date.getDate();
    const year = date.getFullYear();
    let hours = date.getHours();
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    return `${day}, ${d} ${month} ${year} — ${hours}:${minutes}:${seconds} ${ampm}`;
}

function initTheme() {
    const saved = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    isDark.value = saved === 'dark' || (!saved && prefersDark);
    document.documentElement.classList.toggle('dark', isDark.value);
}

function toggleTheme() {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
}

onMounted(() => {
    initTheme();
    currentTime.value = formatDateTime(new Date());
    intervalId = setInterval(() => {
        currentTime.value = formatDateTime(new Date());
    }, 1000);
});

onUnmounted(() => {
    clearInterval(intervalId);
});

function logout() {
    router.post(route('logout'));
}
</script>
