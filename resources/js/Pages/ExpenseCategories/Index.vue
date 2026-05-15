<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto px-6 py-10">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Expense Categories</h1>

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
                    v-for="category in categories"
                    :key="category.id"
                    class="flex items-center gap-3 px-4 py-3"
                >
                    <template v-if="editingId === category.id">
                        <!-- Color picker -->
                        <input
                            type="color"
                            v-model="editColor"
                            class="w-6 h-6 rounded cursor-pointer border-0 bg-transparent p-0 shrink-0"
                            title="Pick color"
                        />
                        <input
                            v-model="editName"
                            @keydown.enter="saveEdit(category)"
                            @keydown.escape="cancelEdit"
                            class="flex-1 text-sm bg-transparent border-b border-notes-yellow outline-none text-gray-900 dark:text-white py-0.5"
                            autofocus
                        />
                        <button @click="saveEdit(category)" class="text-xs font-medium text-notes-yellow hover:opacity-80 transition-opacity">Save</button>
                        <button @click="cancelEdit" class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Cancel</button>
                    </template>
                    <template v-else>
                        <!-- Color swatch -->
                        <span
                            class="w-3 h-3 rounded-full shrink-0 border border-black/10 dark:border-white/10"
                            :style="category.color ? { backgroundColor: category.color } : { backgroundColor: '#d1d5db' }"
                        />
                        <span class="flex-1 text-sm text-gray-900 dark:text-white">{{ category.name }}</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ category.expenses_count }} expense{{ category.expenses_count === 1 ? '' : 's' }}</span>
                        <button
                            @click="startEdit(category)"
                            class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors px-1.5 py-1 rounded"
                        >
                            Edit
                        </button>
                        <button
                            @click="confirmDelete(category)"
                            class="text-xs text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors px-1.5 py-1 rounded"
                        >
                            Delete
                        </button>
                    </template>
                </div>

                <!-- Empty state -->
                <div v-if="categories.length === 0" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                    No categories yet.
                </div>

                <!-- Add new -->
                <div class="flex items-center gap-3 px-4 py-3">
                    <input
                        type="color"
                        v-model="newColor"
                        class="w-6 h-6 rounded cursor-pointer border-0 bg-transparent p-0 shrink-0"
                        title="Pick color"
                    />
                    <input
                        v-model="newName"
                        @keydown.enter="addCategory"
                        placeholder="Add category…"
                        class="flex-1 text-sm bg-transparent outline-none text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600"
                    />
                    <button
                        @click="addCategory"
                        :disabled="!newName.trim()"
                        class="text-xs font-medium text-notes-yellow hover:opacity-80 transition-opacity disabled:opacity-30"
                    >
                        Add
                    </button>
                </div>
            </div>

            <!-- Delete confirmation modal -->
            <div v-if="deletingCategory" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 dark:bg-black/60">
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-black/8 dark:border-white/8 p-6 max-w-sm w-full mx-4 shadow-xl">
                    <p class="text-sm text-gray-900 dark:text-white font-medium mb-1">Delete "{{ deletingCategory.name }}"?</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">This cannot be undone if it is not in use by any expense.</p>
                    <div class="flex gap-3 justify-end">
                        <button @click="deletingCategory = null" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-1.5 rounded-lg hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            Cancel
                        </button>
                        <button @click="deleteCategory" class="text-sm font-medium text-white bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-lg transition-colors">
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

defineProps({
    categories: Array,
});

const newName = ref('');
const newColor = ref('#6366f1');
const editingId = ref(null);
const editName = ref('');
const editColor = ref('');
const deletingCategory = ref(null);

function addCategory() {
    if (!newName.value.trim()) {
        return;
    }
    router.post(route('expense-categories.store'), {
        name: newName.value.trim(),
        color: newColor.value,
    }, {
        onSuccess: () => { newName.value = ''; newColor.value = '#6366f1'; },
        preserveScroll: true,
    });
}

function startEdit(category) {
    editingId.value = category.id;
    editName.value = category.name;
    editColor.value = category.color ?? '#6366f1';
}

function cancelEdit() {
    editingId.value = null;
    editName.value = '';
    editColor.value = '';
}

function saveEdit(category) {
    if (!editName.value.trim()) {
        cancelEdit();
        return;
    }
    router.put(route('expense-categories.update', category.id), {
        name: editName.value.trim(),
        color: editColor.value || null,
    }, {
        onSuccess: cancelEdit,
        preserveScroll: true,
    });
}

function confirmDelete(category) {
    deletingCategory.value = category;
}

function deleteCategory() {
    router.delete(route('expense-categories.destroy', deletingCategory.value.id), {
        onSuccess: () => { deletingCategory.value = null; },
        preserveScroll: true,
    });
}
</script>
