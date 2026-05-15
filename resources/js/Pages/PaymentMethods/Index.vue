<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto px-6 py-10">
            <h1 class="text-2xl font-bold text-notes-text mb-6">Payment Methods</h1>

            <!-- Flash messages -->
            <div v-if="$page.props.flash?.success" class="mb-4 px-4 py-2.5 rounded-lg bg-green-900/20 text-green-400 text-sm">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.errors?.delete" class="mb-4 px-4 py-2.5 rounded-lg bg-red-900/20 text-red-400 text-sm">
                {{ $page.props.errors.delete }}
            </div>

            <!-- List -->
            <div class="bg-notes-surface rounded-xl border border-white/8 divide-y divide-white/8">
                <div
                    v-for="method in paymentMethods"
                    :key="method.id"
                    class="flex items-center gap-3 px-4 py-3"
                >
                    <template v-if="editingId === method.id">
                        <input
                            v-model="editName"
                            @keydown.enter="saveEdit(method)"
                            @keydown.escape="cancelEdit"
                            class="flex-1 text-base bg-transparent border-b border-notes-yellow outline-none text-notes-text py-0.5"
                            autofocus
                        />
                        <button @click="saveEdit(method)" class="text-sm font-semibold text-notes-yellow hover:opacity-80 transition-opacity">Save</button>
                        <button @click="cancelEdit" class="text-sm text-notes-secondary hover:text-notes-text transition-colors">Cancel</button>
                    </template>
                    <template v-else>
                        <span class="flex-1 text-base text-notes-text">{{ method.name }}</span>
                        <button
                            @click="startEdit(method)"
                            class="text-sm font-medium text-notes-secondary hover:text-notes-text transition-colors px-1.5 py-1 rounded"
                        >
                            Edit
                        </button>
                        <button
                            @click="confirmDelete(method)"
                            class="text-sm font-medium text-red-400 hover:text-red-300 transition-colors px-1.5 py-1 rounded"
                        >
                            Delete
                        </button>
                    </template>
                </div>

                <!-- Empty state -->
                <div v-if="paymentMethods.length === 0" class="px-4 py-8 text-center text-base text-notes-secondary">
                    No payment methods yet.
                </div>

                <!-- Add new -->
                <div class="flex items-center gap-3 px-4 py-3">
                    <input
                        v-model="newName"
                        @keydown.enter="addMethod"
                        placeholder="Add payment method…"
                        class="flex-1 text-base bg-transparent outline-none text-notes-text placeholder-notes-secondary/50"
                    />
                    <button
                        @click="addMethod"
                        :disabled="!newName.trim()"
                        class="text-sm font-semibold text-notes-yellow hover:opacity-80 transition-opacity disabled:opacity-30"
                    >
                        Add
                    </button>
                </div>
            </div>

            <!-- Delete confirmation modal -->
            <div v-if="deletingMethod" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
                <div class="bg-notes-elevated rounded-xl border border-white/8 p-6 max-w-sm w-full mx-4 shadow-xl">
                    <p class="text-base text-notes-text font-semibold mb-1">Delete "{{ deletingMethod.name }}"?</p>
                    <p class="text-sm text-notes-secondary mb-5">This cannot be undone if it is not in use.</p>
                    <div class="flex gap-3 justify-end">
                        <button @click="deletingMethod = null" class="text-sm font-medium text-notes-secondary hover:text-notes-text px-3 py-1.5 rounded-lg hover:bg-white/5 transition-colors">
                            Cancel
                        </button>
                        <button @click="deleteMethod" class="text-sm font-semibold text-white bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-lg transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    paymentMethods: Array,
});

const newName = ref('');
const editingId = ref(null);
const editName = ref('');
const deletingMethod = ref(null);

function addMethod() {
    if (!newName.value.trim()) {
        return;
    }
    router.post(route('payment-methods.store'), { name: newName.value.trim() }, {
        onSuccess: () => { newName.value = ''; },
        preserveScroll: true,
    });
}

function startEdit(method) {
    editingId.value = method.id;
    editName.value = method.name;
}

function cancelEdit() {
    editingId.value = null;
    editName.value = '';
}

function saveEdit(method) {
    if (!editName.value.trim() || editName.value.trim() === method.name) {
        cancelEdit();
        return;
    }
    router.put(route('payment-methods.update', method.id), { name: editName.value.trim() }, {
        onSuccess: cancelEdit,
        preserveScroll: true,
    });
}

function confirmDelete(method) {
    deletingMethod.value = method;
}

function deleteMethod() {
    router.delete(route('payment-methods.destroy', deletingMethod.value.id), {
        onSuccess: () => { deletingMethod.value = null; },
        preserveScroll: true,
    });
}
</script>
