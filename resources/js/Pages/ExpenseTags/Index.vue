<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto px-6 py-10">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Expense Tags</h1>

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
                    v-for="tag in tags"
                    :key="tag.id"
                    class="px-4 py-3"
                >
                    <template v-if="editingId === tag.id">
                        <div class="flex items-center gap-3 mb-2">
                            <input
                                v-model="editName"
                                @keydown.escape="cancelEdit"
                                class="flex-1 text-sm bg-transparent border-b border-notes-yellow outline-none text-gray-900 dark:text-white py-0.5"
                                autofocus
                            />
                            <button @click="saveEdit(tag)" class="text-xs font-medium text-notes-yellow hover:opacity-80 transition-opacity">Save</button>
                            <button @click="cancelEdit" class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Cancel</button>
                        </div>
                        <!-- Category multi-select -->
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <button
                                v-for="cat in categories"
                                :key="cat.id"
                                @click="toggleEditCategory(cat.id)"
                                class="text-xs px-2 py-0.5 rounded-full border transition-colors"
                                :class="editCategoryIds.includes(cat.id)
                                    ? 'bg-notes-yellow/20 border-notes-yellow text-amber-800 dark:text-notes-yellow'
                                    : 'border-black/10 dark:border-white/10 text-gray-500 dark:text-gray-400 hover:border-gray-400'"
                            >
                                {{ cat.name }}
                            </button>
                        </div>
                    </template>
                    <template v-else>
                        <div class="flex items-center gap-3">
                            <span class="flex-1 text-sm text-gray-900 dark:text-white">{{ tag.name }}</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ tag.expenses_count }} expense{{ tag.expenses_count === 1 ? '' : 's' }}</span>
                            <button
                                @click="startEdit(tag)"
                                class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors px-1.5 py-1 rounded"
                            >
                                Edit
                            </button>
                            <button
                                @click="confirmDelete(tag)"
                                class="text-xs text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors px-1.5 py-1 rounded"
                            >
                                Delete
                            </button>
                        </div>
                        <!-- Category pills -->
                        <div v-if="tag.categories.length" class="flex flex-wrap gap-1 mt-1.5">
                            <span
                                v-for="cat in tag.categories"
                                :key="cat.id"
                                class="text-xs px-2 py-0.5 rounded-full bg-black/5 dark:bg-white/5 text-gray-500 dark:text-gray-400"
                            >
                                {{ cat.name }}
                            </span>
                        </div>
                    </template>
                </div>

                <!-- Empty state -->
                <div v-if="tags.length === 0" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                    No tags yet.
                </div>

                <!-- Add new -->
                <div class="px-4 py-3">
                    <div class="flex items-center gap-3 mb-2">
                        <input
                            v-model="newName"
                            @keydown.enter="addTag"
                            placeholder="Add tag…"
                            class="flex-1 text-sm bg-transparent outline-none text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600"
                        />
                        <button
                            @click="addTag"
                            :disabled="!newName.trim()"
                            class="text-xs font-medium text-notes-yellow hover:opacity-80 transition-opacity disabled:opacity-30"
                        >
                            Add
                        </button>
                    </div>
                    <!-- Category chips for new tag -->
                    <div v-if="categories.length" class="flex flex-wrap gap-1.5">
                        <button
                            v-for="cat in categories"
                            :key="cat.id"
                            @click="toggleNewCategory(cat.id)"
                            class="text-xs px-2 py-0.5 rounded-full border transition-colors"
                            :class="newCategoryIds.includes(cat.id)
                                ? 'bg-notes-yellow/20 border-notes-yellow text-amber-800 dark:text-notes-yellow'
                                : 'border-black/10 dark:border-white/10 text-gray-500 dark:text-gray-400 hover:border-gray-400'"
                        >
                            {{ cat.name }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Delete confirmation modal -->
            <div v-if="deletingTag" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 dark:bg-black/60">
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-black/8 dark:border-white/8 p-6 max-w-sm w-full mx-4 shadow-xl">
                    <p class="text-sm text-gray-900 dark:text-white font-medium mb-1">Delete "{{ deletingTag.name }}"?</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">This cannot be undone if the tag is not in use by any expense.</p>
                    <div class="flex gap-3 justify-end">
                        <button @click="deletingTag = null" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-1.5 rounded-lg hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                            Cancel
                        </button>
                        <button @click="deleteTag" class="text-sm font-medium text-white bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-lg transition-colors">
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
    tags: Array,
    categories: Array,
});

const newName = ref('');
const newCategoryIds = ref([]);
const editingId = ref(null);
const editName = ref('');
const editCategoryIds = ref([]);
const deletingTag = ref(null);

function toggleNewCategory(id) {
    const idx = newCategoryIds.value.indexOf(id);
    if (idx === -1) {
        newCategoryIds.value.push(id);
    } else {
        newCategoryIds.value.splice(idx, 1);
    }
}

function toggleEditCategory(id) {
    const idx = editCategoryIds.value.indexOf(id);
    if (idx === -1) {
        editCategoryIds.value.push(id);
    } else {
        editCategoryIds.value.splice(idx, 1);
    }
}

function addTag() {
    if (!newName.value.trim()) {
        return;
    }
    router.post(route('expense-tags.store'), {
        name: newName.value.trim(),
        category_ids: newCategoryIds.value,
    }, {
        onSuccess: () => { newName.value = ''; newCategoryIds.value = []; },
        preserveScroll: true,
    });
}

function startEdit(tag) {
    editingId.value = tag.id;
    editName.value = tag.name;
    editCategoryIds.value = tag.categories.map((c) => c.id);
}

function cancelEdit() {
    editingId.value = null;
    editName.value = '';
    editCategoryIds.value = [];
}

function saveEdit(tag) {
    if (!editName.value.trim()) {
        cancelEdit();
        return;
    }
    router.put(route('expense-tags.update', tag.id), {
        name: editName.value.trim(),
        category_ids: editCategoryIds.value,
    }, {
        onSuccess: cancelEdit,
        preserveScroll: true,
    });
}

function confirmDelete(tag) {
    deletingTag.value = tag;
}

function deleteTag() {
    router.delete(route('expense-tags.destroy', deletingTag.value.id), {
        onSuccess: () => { deletingTag.value = null; },
        preserveScroll: true,
    });
}
</script>
