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
                    <svg v-if="!isDark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
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

        <!-- Body -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Desktop sidebar -->
            <aside class="hidden md:flex flex-col w-56 shrink-0 bg-white dark:bg-gray-900 border-r border-black/8 dark:border-white/8">
                <nav class="flex-1 py-3 px-2 space-y-0.5">
                    <!-- Dashboard -->
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-sm transition-colors"
                        :class="isActive('/dashboard')
                            ? 'bg-black/8 dark:bg-white/8 text-gray-900 dark:text-white font-medium'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-black/5 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white'"
                    >
                        <!-- House icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        Dashboard
                    </Link>

                    <!-- Expense Manager group -->
                    <div>
                        <button
                            @click="expenseGroupOpen = !expenseGroupOpen"
                            class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-md text-sm transition-colors"
                            :class="isOnExpenseSection
                                ? 'text-gray-900 dark:text-white font-medium'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-black/5 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white'"
                        >
                            <!-- Receipt icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="1"/>
                                <line x1="9" y1="12" x2="15" y2="12"/>
                                <line x1="9" y1="16" x2="13" y2="16"/>
                            </svg>
                            <span class="flex-1 text-left">Expense Manager</span>
                            <!-- Chevron -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transition-transform" :class="expenseGroupOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>

                        <div v-show="expenseGroupOpen" class="mt-0.5 ml-3 pl-3 border-l border-black/8 dark:border-white/8 space-y-0.5">
                            <Link
                                href="/expenses"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm transition-colors"
                                :class="isActive('/expenses')
                                    ? 'bg-black/8 dark:bg-white/8 text-gray-900 dark:text-white font-medium'
                                    : 'text-gray-500 dark:text-gray-400 hover:bg-black/5 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white'"
                            >
                                Expenses
                            </Link>
                            <Link
                                href="/expense-categories"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm transition-colors"
                                :class="isActive('/expense-categories')
                                    ? 'bg-black/8 dark:bg-white/8 text-gray-900 dark:text-white font-medium'
                                    : 'text-gray-500 dark:text-gray-400 hover:bg-black/5 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white'"
                            >
                                Categories
                            </Link>
                            <Link
                                href="/expense-tags"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm transition-colors"
                                :class="isActive('/expense-tags')
                                    ? 'bg-black/8 dark:bg-white/8 text-gray-900 dark:text-white font-medium'
                                    : 'text-gray-500 dark:text-gray-400 hover:bg-black/5 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white'"
                            >
                                Tags
                            </Link>
                            <Link
                                href="/payment-methods"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm transition-colors"
                                :class="isActive('/payment-methods')
                                    ? 'bg-black/8 dark:bg-white/8 text-gray-900 dark:text-white font-medium'
                                    : 'text-gray-500 dark:text-gray-400 hover:bg-black/5 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-white'"
                            >
                                Payment Methods
                            </Link>
                        </div>
                    </div>
                </nav>
            </aside>

            <!-- Main content -->
            <main class="flex-1 overflow-y-auto pb-16 md:pb-0">
                <slot />
            </main>
        </div>

        <!-- Mobile bottom nav -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 z-20 bg-white dark:bg-gray-900 border-t border-black/8 dark:border-white/8 flex">
            <Link
                :href="route('dashboard')"
                class="flex-1 flex flex-col items-center gap-0.5 py-2 text-xs transition-colors"
                :class="isActive('/dashboard')
                    ? 'text-gray-900 dark:text-white'
                    : 'text-gray-400 dark:text-gray-500'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                <span>Dashboard</span>
            </Link>
            <Link
                href="/expenses"
                class="flex-1 flex flex-col items-center gap-0.5 py-2 text-xs transition-colors"
                :class="isOnExpenseSection
                    ? 'text-gray-900 dark:text-white'
                    : 'text-gray-400 dark:text-gray-500'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                    <line x1="9" y1="12" x2="15" y2="12"/>
                    <line x1="9" y1="16" x2="13" y2="16"/>
                </svg>
                <span>Expenses</span>
            </Link>
        </nav>

        <!-- Mobile FAB — add expense -->
        <Link
            href="/expenses/create"
            class="md:hidden fixed bottom-20 right-4 z-30 w-14 h-14 rounded-full bg-notes-yellow flex items-center justify-center shadow-lg active:scale-95 transition-transform"
            aria-label="Add expense"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
        </Link>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';

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

const currentPath = computed(() => page.url);

function isActive(path) {
    return currentPath.value === path || currentPath.value.startsWith(path + '/') || currentPath.value.startsWith(path + '?');
}

const isOnExpenseSection = computed(() =>
    currentPath.value.startsWith('/expenses') ||
    currentPath.value.startsWith('/expense-categories') ||
    currentPath.value.startsWith('/expense-tags') ||
    currentPath.value.startsWith('/payment-methods')
);

const expenseGroupOpen = ref(false);

onMounted(() => {
    expenseGroupOpen.value = isOnExpenseSection.value;
});
</script>
