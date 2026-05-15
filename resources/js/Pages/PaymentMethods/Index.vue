<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto px-6 py-10">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Payment Methods</h1>

            <!-- Flash messages -->
            <div v-if="$page.props.flash?.success" class="mb-4 px-4 py-2.5 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.errors?.delete" class="mb-4 px-4 py-2.5 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-sm">
                {{ $page.props.errors.delete }}
            </div>

            <!-- List -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-black/8 dark:border-white/8 divide-y divide-black/8 dark:divide-white/8">
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
                            class="flex-1 text-sm bg-transparent border-b border-notes-yellow dark:border-notes-yellow outline-none text-gray-900 dark:text-white py-0.5"
                            autofocus
                        />
                        <button @click="saveEdit(method)" class="text-xs font-medium text-notes-yellow hover:opacity-80 transition-opacity">Save</button>
                        <button @click="cancelEdit" class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Cancel</button>
                    </template>
                    <template v-else>
                        <span class="flex-1 text-sm text-gray-900 dark:text-white">{{ method.name }}</span>
                        <button
                            @click="startEdit(method)"
                            class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors px-1.5 py-1 rounded"
                        >
                            Edit
                        </button>
                        <button
                            @click="confirmDelete(method)"
                            class="text-xs text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors px-1.5 py-1 rounded"
                        >
                            Delete
                        </button>
                    </template>
                </div>

                <!-- Empty state -->
                <div v-if="paymentMethods.length === 0" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                    No payment methods yet.
                </div>

                <!-- Add new -->
                <div class="flex items-center gap-3 px-4 py-3">
                    <input
                        v-model="newName"
                        @keydown.enter="addMethod"
                        placeholder="Add payment method…"
                        class="flex-1 text-sm bg-transparent outline-none text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600"
                    />
                    <button
                        @click="addMethod"
                        :disabled="!newName.trim()"
                        class="text-xs font-medium text-notes-yellow hover:opacity-80 transition-opacity disabled:opacity-30"
                    >
                        Add
                    </button>
                </div>
            </div>

            <!-- Delete confirmation modal -->
            <div v-if="deletingMethod" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 dark:bg-black/60">
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-black/8 dark:border-white/8 p-6 max-w-sm w-full mx-4 shadow-xl">
                    <p class="text-sm text-gray-900 dark:text-white font-medium mb-1">Delete "{{ deletingMethod.name }}"?</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">This cannot be undone if it is not in use.</p>
                    <div class="flex gap-3 justify-end">
                        <button @click="deletingMethod = null" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-1.5 rounded-lg hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            Cancel
                        </button>
                        <button @click="deleteMethod" class="text-sm font-medium text-white bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-lg transition-colors">
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
