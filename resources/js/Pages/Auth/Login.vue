<template>
    <div class="notes-bg min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-sm">
            <!-- App icon / title -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-notes-yellow shadow-lg mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-amber-900" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Zm-7 3a1 1 0 0 1 1 1v1h1a1 1 0 0 1 0 2h-1v1a1 1 0 0 1-2 0v-1H9a1 1 0 0 1 0-2h1V7a1 1 0 0 1 1-1Zm-4 8h8a1 1 0 0 1 0 2H8a1 1 0 0 1 0-2Zm0 3h5a1 1 0 0 1 0 2H8a1 1 0 0 1 0-2Z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold text-notes-text tracking-tight">Home Manager</h1>
                <p class="text-sm text-notes-secondary mt-1">Enter your PIN to continue</p>
            </div>

            <!-- PIN dots display -->
            <div class="flex justify-center gap-4 mb-8">
                <div
                    v-for="i in 6"
                    :key="i"
                    :class="[
                        'w-3.5 h-3.5 rounded-full transition-all duration-150',
                        pin.length >= i
                            ? 'bg-notes-yellow scale-110'
                            : 'bg-notes-divider'
                    ]"
                />
            </div>

            <!-- Error message -->
            <p v-if="error" class="text-center text-sm text-red-500 mb-4 animate-shake">
                {{ error }}
            </p>

            <!-- Numpad -->
            <div class="grid grid-cols-3 gap-3">
                <button
                    v-for="key in numpadKeys"
                    :key="key"
                    @click="handleKey(key)"
                    :disabled="processing"
                    :class="[
                        'numpad-btn',
                        key === 'del' ? 'text-notes-secondary' : 'text-notes-text',
                        key === '' ? 'invisible' : '',
                    ]"
                >
                    <span v-if="key === 'del'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l-3-3m0 0l3-3m-3 3h8m-8 6H6a2 2 0 01-2-2V7a2 2 0 012-2h4l6 6-6 6z" />
                        </svg>
                    </span>
                    <span v-else class="text-xl font-medium">{{ key }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const pin = ref('');
const processing = ref(false);

const form = useForm({ pin: '' });

const error = computed(() => form.errors.pin ?? null);

const numpadKeys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '', '0', 'del'];

function handleKey(key) {
    if (key === 'del') {
        pin.value = pin.value.slice(0, -1);
        form.clearErrors();
        return;
    }
    if (pin.value.length >= 6) return;
    pin.value += key;
}

watch(pin, (val) => {
    if (val.length === 6) {
        submit();
    }
});

function submit() {
    processing.value = true;
    form.pin = pin.value;
    form.post(route('login.authenticate'), {
        onError: () => {
            pin.value = '';
            processing.value = false;
        },
        onSuccess: () => {
            processing.value = false;
        },
    });
}
</script>

<style scoped>
@reference "../../../css/app.css";

.notes-bg {
    background-color: #1C1C1E;
    font-family: -apple-system, 'SF Pro Text', 'Helvetica Neue', sans-serif;
}

.numpad-btn {
    @apply rounded-2xl py-4 text-center border border-white/8
           active:scale-95 active:opacity-60
           transition-all duration-100 cursor-pointer
           disabled:opacity-30 disabled:cursor-not-allowed;
    background-color: #2C2C2E;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20% { transform: translateX(-6px); }
    40% { transform: translateX(6px); }
    60% { transform: translateX(-4px); }
    80% { transform: translateX(4px); }
}

.animate-shake {
    animation: shake 0.4s ease-in-out;
}
</style>
