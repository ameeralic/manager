<template>
    <AppLayout>
        <div class="max-w-xl mx-auto px-6 py-10">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-8">Add Expense</h1>

            <form @submit.prevent="submit" class="space-y-6">
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
                        <!-- Selected category badge -->
                        <div v-if="selectedCategory && !categoryOpen" class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <span class="flex items-center gap-1.5 text-sm text-gray-900 dark:text-white">
                                <span v-if="selectedCategory.color" class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: selectedCategory.color }" />
                                {{ selectedCategory.name }}
                            </span>
                        </div>

                        <!-- Dropdown -->
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

                            <!-- Create option -->
                            <div
                                v-if="categorySearch.trim() && !exactCategoryMatch"
                                @mousedown.prevent="startQuickCreate"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm text-notes-yellow hover:bg-black/5 dark:hover:bg-white/5 cursor-pointer border-t border-black/8 dark:border-white/8"
                            >
                                <span>Create "{{ categorySearch.trim() }}"</span>
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

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3 rounded-xl bg-notes-yellow text-amber-900 font-semibold text-sm hover:opacity-90 active:scale-[.99] transition-all disabled:opacity-50"
                >
                    Save Expense
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatINR } from '@/composables/useIndianCurrency.js';

const props = defineProps({
    categories: Array,
    paymentMethods: Array,
});

const form = useForm({
    title: '',
    expense_category_id: '',
    payment_method_id: '',
    paid_by: 'Ameer',
    amount: '',
    tag_ids: [],
});

// Category search / dropdown
const categorySearch = ref('');
const categoryOpen = ref(false);
const selectedCategory = ref(null);

const filteredCategories = computed(() => {
    if (!categorySearch.value.trim()) {
        return props.categories;
    }
    const q = categorySearch.value.toLowerCase();
    return props.categories.filter((c) => c.name.toLowerCase().includes(q));
});

const exactCategoryMatch = computed(() =>
    props.categories.some((c) => c.name.toLowerCase() === categorySearch.value.trim().toLowerCase())
);

function selectCategory(cat) {
    selectedCategory.value = cat;
    form.expense_category_id = cat.id;
    categorySearch.value = '';
    categoryOpen.value = false;
    form.tag_ids = [];
}

function onCategoryBlur() {
    setTimeout(() => { categoryOpen.value = false; }, 150);
}

// Tags filtered by selected category
const availableTags = computed(() => {
    if (!selectedCategory.value) {
        return [];
    }
    const catTags = selectedCategory.value.tags ?? [];
    return catTags.length > 0 ? catTags : [];
});

function toggleTag(id) {
    const idx = form.tag_ids.indexOf(id);
    if (idx === -1) {
        form.tag_ids.push(id);
    } else {
        form.tag_ids.splice(idx, 1);
    }
}

// Quick-create category
const quickCreateMode = ref(false);
const quickCreateName = ref('');
const quickCreateInput = ref(null);

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
        props.categories.push(newCategory);
        selectCategory(newCategory);
        cancelQuickCreate();
    }
}

// Amount formatting
function formatAmountOnBlur() {
    const raw = parseFloat(String(form.amount).replace(/[^0-9.]/g, ''));
    if (!isNaN(raw)) {
        form.amount = raw.toFixed(2);
    }
}

function submit() {
    form.post(route('expenses.store'));
}
</script>
