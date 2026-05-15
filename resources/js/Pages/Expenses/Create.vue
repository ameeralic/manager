<template>
    <AppLayout>
        <div class="max-w-xl mx-auto px-6 py-10">
            <h1 class="text-2xl font-bold text-notes-text mb-8">Add Expense</h1>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-notes-secondary mb-1.5">Title <span class="font-normal">(optional)</span></label>
                    <input
                        v-model="form.title"
                        type="text"
                        placeholder="e.g. Grocery run"
                        class="w-full text-base bg-notes-elevated border border-white/10 rounded-lg px-3 py-2.5 text-notes-text placeholder-notes-secondary/50 outline-none focus:ring-2 focus:ring-notes-yellow/40"
                    />
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-notes-secondary mb-1.5">Category</label>
                    <div class="relative">
                        <input
                            v-model="categorySearch"
                            @focus="categoryOpen = true"
                            @blur="onCategoryBlur"
                            type="text"
                            placeholder="Search or create category…"
                            class="w-full text-base bg-notes-elevated border border-white/10 rounded-lg px-3 py-2.5 text-notes-text placeholder-notes-secondary/50 outline-none focus:ring-2 focus:ring-notes-yellow/40"
                            :class="{ 'ring-2 ring-notes-yellow/40': categoryOpen }"
                        />
                        <div v-if="selectedCategory && !categoryOpen" class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <span class="text-base text-notes-text">{{ selectedCategory.name }}</span>
                        </div>

                        <div v-if="categoryOpen" class="absolute z-20 mt-1 w-full bg-notes-elevated border border-white/10 rounded-lg shadow-lg overflow-hidden">
                            <div
                                v-for="cat in filteredCategories"
                                :key="cat.id"
                                @mousedown.prevent="selectCategory(cat)"
                                class="px-3 py-2.5 text-base text-notes-text hover:bg-white/5 cursor-pointer"
                            >
                                {{ cat.name }}
                            </div>

                            <div
                                v-if="categorySearch.trim() && !exactCategoryMatch"
                                @mousedown.prevent="startQuickCreate"
                                class="px-3 py-2.5 text-base text-notes-yellow hover:bg-white/5 cursor-pointer border-t border-white/8"
                            >
                                Create "{{ categorySearch.trim() }}"
                            </div>

                            <div v-if="filteredCategories.length === 0 && !categorySearch.trim()" class="px-3 py-2.5 text-base text-notes-secondary">
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
                            class="flex-1 text-base bg-notes-elevated border border-notes-yellow/60 rounded-lg px-3 py-2 text-notes-text placeholder-notes-secondary/50 outline-none focus:ring-2 focus:ring-notes-yellow/40"
                        />
                        <button type="button" @click="submitQuickCreate" class="text-sm font-semibold text-notes-yellow hover:opacity-80 px-2">Create</button>
                        <button type="button" @click="cancelQuickCreate" class="text-sm text-notes-secondary hover:text-notes-text px-1">Cancel</button>
                    </div>
                    <p v-if="form.errors.expense_category_id" class="mt-1 text-sm text-red-400">{{ form.errors.expense_category_id }}</p>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-notes-secondary mb-1.5">Tags</label>
                    <div class="flex flex-wrap gap-1.5">
                        <button
                            v-for="tag in tags"
                            :key="tag.id"
                            type="button"
                            @click="toggleTag(tag.id)"
                            class="text-sm px-2.5 py-1 rounded-full border transition-colors"
                            :class="form.tag_ids.includes(tag.id)
                                ? 'bg-notes-yellow/20 border-notes-yellow text-notes-yellow font-semibold'
                                : 'border-white/10 text-notes-secondary hover:border-white/30'"
                        >
                            {{ tag.name }}
                        </button>
                        <span v-if="tags.length === 0" class="text-sm text-notes-secondary">No tags available.</span>
                    </div>
                </div>

                <!-- Paid By -->
                <div>
                    <label class="block text-sm font-medium text-notes-secondary mb-1.5">Paid By</label>
                    <div class="flex gap-3">
                        <label
                            v-for="person in ['Ameer', 'Jinsi']"
                            :key="person"
                            class="flex-1 flex items-center justify-center gap-2 border rounded-lg py-2.5 cursor-pointer transition-colors text-base font-medium"
                            :class="form.paid_by === person
                                ? 'border-notes-yellow bg-notes-yellow/10 text-notes-text'
                                : 'border-white/10 text-notes-secondary hover:border-white/30'"
                        >
                            <input type="radio" v-model="form.paid_by" :value="person" class="sr-only" />
                            {{ person }}
                        </label>
                    </div>
                    <p v-if="form.errors.paid_by" class="mt-1 text-sm text-red-400">{{ form.errors.paid_by }}</p>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-notes-secondary mb-1.5">Payment Method</label>
                    <select
                        v-model="form.payment_method_id"
                        class="w-full text-base bg-notes-elevated border border-white/10 rounded-lg px-3 py-2.5 text-notes-text outline-none focus:ring-2 focus:ring-notes-yellow/40"
                    >
                        <option value="" disabled>Select payment method…</option>
                        <option v-for="method in paymentMethods" :key="method.id" :value="method.id">{{ method.name }}</option>
                    </select>
                    <p v-if="form.errors.payment_method_id" class="mt-1 text-sm text-red-400">{{ form.errors.payment_method_id }}</p>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-notes-secondary mb-1.5">Amount</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-base text-notes-secondary pointer-events-none">₹</span>
                        <input
                            v-model="form.amount"
                            @blur="formatAmountOnBlur"
                            type="text"
                            inputmode="decimal"
                            placeholder="0.00"
                            class="w-full text-base bg-notes-elevated border border-white/10 rounded-lg pl-7 pr-3 py-2.5 text-notes-text placeholder-notes-secondary/50 outline-none focus:ring-2 focus:ring-notes-yellow/40"
                        />
                    </div>
                    <p v-if="form.errors.amount" class="mt-1 text-sm text-red-400">{{ form.errors.amount }}</p>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3 rounded-xl bg-notes-yellow text-amber-900 font-bold text-base hover:opacity-90 active:scale-[.99] transition-all disabled:opacity-50"
                >
                    Save Expense
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatINR } from '@/composables/useIndianCurrency.js';

const props = defineProps({
    categories: Array,
    tags: Array,
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
}

function onCategoryBlur() {
    setTimeout(() => { categoryOpen.value = false; }, 150);
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
        props.categories.push(newCategory);
        selectCategory(newCategory);
        cancelQuickCreate();
    }
}

function toggleTag(id) {
    const idx = form.tag_ids.indexOf(id);
    if (idx === -1) {
        form.tag_ids.push(id);
    } else {
        form.tag_ids.splice(idx, 1);
    }
}

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
