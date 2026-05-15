<template>
    <div class="space-y-6">
        <!-- Title -->
        <div>
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Title <span class="font-normal">(optional)</span></label>
            <input
                v-model="form.title"
                type="text"
                placeholder="e.g. Grocery run"
                class="w-full text-sm bg-white dark:bg-gray-900 border border-black/10 dark:border-white/10 rounded-lg px-3 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:ring-2 focus:ring-notes-yellow/40"
            />
        </div>

        <!-- Category -->
        <div>
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Category</label>
            <div class="relative">
                <input
                    v-model="categorySearch"
                    @focus="categoryOpen = true"
                    @blur="onCategoryBlur"
                    type="text"
                    placeholder="Search or create category…"
                    class="w-full text-sm bg-white dark:bg-gray-900 border border-black/10 dark:border-white/10 rounded-lg px-3 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:ring-2 focus:ring-notes-yellow/40"
                    :class="{ 'ring-2 ring-notes-yellow/40': categoryOpen }"
                />
                <div v-if="selectedCategory && !categoryOpen" class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <span class="flex items-center gap-1.5 text-sm text-gray-900 dark:text-white">
                        <span v-if="selectedCategory.color" class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: selectedCategory.color }" />
                        {{ selectedCategory.name }}
                    </span>
                </div>

                <div v-if="categoryOpen" class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-900 border border-black/10 dark:border-white/10 rounded-lg shadow-lg overflow-hidden">
                    <div
                        v-for="cat in filteredCategories"
                        :key="cat.id"
                        @mousedown.prevent="selectCategory(cat)"
                        class="flex items-center gap-2 px-3 py-2.5 text-sm text-gray-900 dark:text-white hover:bg-black/5 dark:hover:bg-white/5 cursor-pointer"
                    >
                        <span v-if="cat.color" class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: cat.color }" />
                        <span>{{ cat.name }}</span>
                    </div>
                    <div
                        v-if="categorySearch.trim() && !exactCategoryMatch"
                        @mousedown.prevent="startQuickCreate"
                        class="flex items-center gap-2 px-3 py-2.5 text-sm text-notes-yellow hover:bg-black/5 dark:hover:bg-white/5 cursor-pointer border-t border-black/8 dark:border-white/8"
                    >
                        Create "{{ categorySearch.trim() }}"
                    </div>
                    <div v-if="filteredCategories.length === 0 && !categorySearch.trim()" class="px-3 py-2.5 text-sm text-gray-400 dark:text-gray-600">
                        No categories yet.
                    </div>
                </div>
            </div>

            <!-- Inline quick-create -->
            <div v-if="quickCreateMode" class="mt-2 flex items-center gap-2">
                <input
                    v-model="quickCreateName"
                    ref="quickCreateInput"
                    @keydown.enter.prevent="submitQuickCreate"
                    @keydown.escape="cancelQuickCreate"
                    placeholder="Category name…"
                    class="flex-1 text-sm bg-white dark:bg-gray-900 border border-notes-yellow/60 rounded-lg px-3 py-2 text-gray-900 dark:text-white placeholder-gray-400 outline-none focus:ring-2 focus:ring-notes-yellow/40"
                />
                <button type="button" @click="submitQuickCreate" class="text-xs font-medium text-notes-yellow hover:opacity-80 px-2">Create</button>
                <button type="button" @click="cancelQuickCreate" class="text-xs text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 px-1">Cancel</button>
            </div>
            <p v-if="form.errors.expense_category_id" class="mt-1 text-xs text-red-500">{{ form.errors.expense_category_id }}</p>
        </div>

        <!-- Tags -->
        <div>
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Tags</label>
            <div class="flex flex-wrap gap-1.5">
                <button
                    v-for="tag in availableTags"
                    :key="tag.id"
                    type="button"
                    @click="toggleTag(tag.id)"
                    class="text-xs px-2.5 py-1 rounded-full border transition-colors"
                    :class="form.tag_ids.includes(tag.id)
                        ? 'bg-notes-yellow/20 border-notes-yellow text-amber-800 dark:text-notes-yellow font-medium'
                        : 'border-black/10 dark:border-white/10 text-gray-600 dark:text-gray-400 hover:border-gray-400'"
                >
                    {{ tag.name }}
                </button>
                <span v-if="availableTags.length === 0" class="text-xs text-gray-400 dark:text-gray-600">
                    {{ selectedCategory ? 'No tags for this category.' : 'Select a category to see tags.' }}
                </span>
            </div>
        </div>

        <!-- Paid By -->
        <div>
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Paid By</label>
            <div class="flex gap-3">
                <label
                    v-for="person in ['Ameer', 'Jinsi']"
                    :key="person"
                    class="flex-1 flex items-center justify-center gap-2 border rounded-lg py-2.5 cursor-pointer transition-colors text-sm"
                    :class="form.paid_by === person
                        ? 'border-notes-yellow bg-notes-yellow/10 text-gray-900 dark:text-white font-medium'
                        : 'border-black/10 dark:border-white/10 text-gray-600 dark:text-gray-400 hover:border-gray-300'"
                >
                    <input type="radio" v-model="form.paid_by" :value="person" class="sr-only" />
                    {{ person }}
                </label>
            </div>
            <p v-if="form.errors.paid_by" class="mt-1 text-xs text-red-500">{{ form.errors.paid_by }}</p>
        </div>

        <!-- Payment Method -->
        <div>
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Payment Method</label>
            <select
                v-model="form.payment_method_id"
                class="w-full text-sm bg-white dark:bg-gray-900 border border-black/10 dark:border-white/10 rounded-lg px-3 py-2.5 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-notes-yellow/40"
            >
                <option value="" disabled>Select payment method…</option>
                <option v-for="method in paymentMethods" :key="method.id" :value="method.id">{{ method.name }}</option>
            </select>
            <p v-if="form.errors.payment_method_id" class="mt-1 text-xs text-red-500">{{ form.errors.payment_method_id }}</p>
        </div>

        <!-- Amount -->
        <div>
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Amount</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-sm text-gray-400 dark:text-gray-500 pointer-events-none">₹</span>
                <input
                    v-model="form.amount"
                    @blur="formatAmountOnBlur"
                    type="text"
                    inputmode="decimal"
                    placeholder="0.00"
                    class="w-full text-sm bg-white dark:bg-gray-900 border border-black/10 dark:border-white/10 rounded-lg pl-7 pr-3 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:ring-2 focus:ring-notes-yellow/40"
                />
            </div>
            <p v-if="form.errors.amount" class="mt-1 text-xs text-red-500">{{ form.errors.amount }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick, watch } from 'vue';

const props = defineProps({
    form: Object,
    categories: Array,
    paymentMethods: Array,
});

// Category state
const localCategories = ref([...props.categories]);
const categorySearch = ref('');
const categoryOpen = ref(false);
const quickCreateMode = ref(false);
const quickCreateName = ref('');
const quickCreateInput = ref(null);

// Initialise selectedCategory from form's pre-set category id
const selectedCategory = ref(
    localCategories.value.find((c) => c.id === props.form.expense_category_id) ?? null
);

watch(() => props.form.expense_category_id, (id) => {
    selectedCategory.value = localCategories.value.find((c) => c.id === id) ?? null;
});

const filteredCategories = computed(() => {
    if (!categorySearch.value.trim()) {
        return localCategories.value;
    }
    const q = categorySearch.value.toLowerCase();
    return localCategories.value.filter((c) => c.name.toLowerCase().includes(q));
});

const exactCategoryMatch = computed(() =>
    localCategories.value.some((c) => c.name.toLowerCase() === categorySearch.value.trim().toLowerCase())
);

function selectCategory(cat) {
    selectedCategory.value = cat;
    props.form.expense_category_id = cat.id;
    props.form.tag_ids = [];
    categorySearch.value = '';
    categoryOpen.value = false;
}

function onCategoryBlur() {
    setTimeout(() => { categoryOpen.value = false; }, 150);
}

function startQuickCreate() {
    quickCreateName.value = categorySearch.value.trim();
    categoryOpen.value = false;
    quickCreateMode.value = true;
    nextTick(() => quickCreateInput.value?.focus());
}

function cancelQuickCreate() {
    quickCreateMode.value = false;
    quickCreateName.value = '';
}

async function submitQuickCreate() {
    if (!quickCreateName.value.trim()) {
        return;
    }
    const response = await fetch(route('expense-categories.quick-create'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ name: quickCreateName.value.trim() }),
    });

    if (response.ok) {
        const newCategory = await response.json();
        newCategory.tags = [];
        localCategories.value.push(newCategory);
        selectCategory(newCategory);
        cancelQuickCreate();
    }
}

// Tags
const availableTags = computed(() => {
    if (!selectedCategory.value) {
        return [];
    }
    return selectedCategory.value.tags ?? [];
});

function toggleTag(id) {
    const idx = props.form.tag_ids.indexOf(id);
    if (idx === -1) {
        props.form.tag_ids.push(id);
    } else {
        props.form.tag_ids.splice(idx, 1);
    }
}

// Amount
function formatAmountOnBlur() {
    const raw = parseFloat(String(props.form.amount).replace(/[^0-9.]/g, ''));
    if (!isNaN(raw)) {
        props.form.amount = raw.toFixed(2);
    }
}
</script>
