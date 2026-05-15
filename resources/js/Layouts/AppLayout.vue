<template>
    <div class="notes-app min-h-screen flex flex-col">
        <!-- Top bar -->
        <header class="notes-header h-12 flex items-center px-5 border-b border-black/8 sticky top-0 z-10">
            <div class="flex-1 flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-notes-yellow flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-amber-800" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-notes-text">Home Manager</span>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs text-notes-secondary tabular-nums">{{ currentTime }}</span>
                <span class="text-notes-secondary/30 text-xs">|</span>
                <span class="text-xs text-notes-secondary">{{ auth.user?.name }}</span>
                <button
                    @click="logout"
                    class="text-xs text-notes-secondary hover:text-notes-text transition-colors px-2 py-1 rounded-md hover:bg-black/5"
                >
                    Sign out
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

onMounted(() => {
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

<style scoped>
.notes-app {
    background-color: #1C1C1E;
    font-family: -apple-system, 'SF Pro Text', 'Helvetica Neue', sans-serif;
}

.notes-header {
    background-color: #2C2C2E;
    border-color: rgba(255, 255, 255, 0.08);
}
</style>
