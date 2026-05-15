<template>
    <AppLayout>
        <div class="max-w-6xl mx-auto px-6 py-10">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-notes-text">Expenses</h1>
                <Link :href="route('expenses.create')" class="text-sm font-semibold bg-notes-yellow text-amber-900 px-4 py-2 rounded-lg hover:opacity-90 transition-opacity">
                    Add Expense
                </Link>
            </div>

            <!-- Flash -->
<<<<<<< HEAD
            <div v-if="$page.props.flash?.success" class="mb-4 px-4 py-2.5 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm">
=======
            <div v-if="$page.props.flash?.success" class="mb-4 px-4 py-2.5 rounded-lg bg-green-900/20 text-green-400 text-sm">
>>>>>>> feature/manager-16-expenses-edit-form-and-update-action
                {{ $page.props.flash.success }}
            </div>

            <!-- Filters -->
            <div class="bg-notes-surface rounded-xl border border-white/8 p-4 mb-6">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    <!-- Date from -->
                    <div>
                        <label class="block text-sm text-notes-secondary mb-1">From</label>
                        <input
                            type="date"
                            v-model="localFilters.date_from"
                            class="w-full text-sm bg-notes-elevated border border-white/10 rounded-lg px-2 py-1.5 text-notes-text outline-none focus:ring-1 focus:ring-notes-yellow/40"
                        />
                    </div>
                    <!-- Date to -->
                    <div>
                        <label class="block text-sm text-notes-secondary mb-1">To</label>
                        <input
                            type="date"
                            v-model="localFilters.date_to"
                            class="w-full text-sm bg-notes-elevated border border-white/10 rounded-lg px-2 py-1.5 text-notes-text outline-none focus:ring-1 focus:ring-notes-yellow/40"
                        />
                    </div>
                    <!-- Category -->
                    <div>
                        <label class="block text-sm text-notes-secondary mb-1">Category</label>
                        <select v-model="localFilters.category_id" class="w-full text-sm bg-notes-elevated border border-white/10 rounded-lg px-2 py-1.5 text-notes-text outline-none focus:ring-1 focus:ring-notes-yellow/40">
                            <option value="">All</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>
                    <!-- Paid By -->
                    <div>
                        <label class="block text-sm text-notes-secondary mb-1">Paid By</label>
                        <select v-model="localFilters.paid_by" class="w-full text-sm bg-notes-elevated border border-white/10 rounded-lg px-2 py-1.5 text-notes-text outline-none focus:ring-1 focus:ring-notes-yellow/40">
                            <option value="">All</option>
                            <option value="Ameer">Ameer</option>
                            <option value="Jinsi">Jinsi</option>
                        </select>
                    </div>
                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm text-notes-secondary mb-1">Payment</label>
                        <select v-model="localFilters.payment_method_id" class="w-full text-sm bg-notes-elevated border border-white/10 rounded-lg px-2 py-1.5 text-notes-text outline-none focus:ring-1 focus:ring-notes-yellow/40">
                            <option value="">All</option>
                            <option v-for="method in paymentMethods" :key="method.id" :value="method.id">{{ method.name }}</option>
                        </select>
                    </div>
                    <!-- Filter actions -->
                    <div class="flex items-end gap-2">
                        <button @click="applyFilters" class="flex-1 text-sm font-semibold bg-notes-yellow text-amber-900 px-3 py-1.5 rounded-lg hover:opacity-90 transition-opacity">
                            Filter
                        </button>
                        <button @click="clearFilters" class="text-sm text-notes-secondary hover:text-notes-text px-2 py-1.5 rounded-lg hover:bg-white/5 transition-colors">
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Tag filter chips -->
                <div v-if="tags.length" class="mt-3 flex flex-wrap gap-1.5">
                    <button
                        v-for="tag in tags"
                        :key="tag.id"
                        @click="toggleTagFilter(tag.id)"
                        class="text-sm px-2.5 py-0.5 rounded-full border transition-colors"
                        :class="localFilters.tag_ids?.includes(tag.id)
                            ? 'bg-notes-yellow/20 border-notes-yellow text-notes-yellow font-semibold'
                            : 'border-white/10 text-notes-secondary hover:border-white/30'"
                    >
                        {{ tag.name }}
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-notes-surface rounded-xl border border-white/8 overflow-hidden">
                <table v-if="expenses.data.length > 0" class="w-full">
                    <thead>
                        <tr class="border-b border-white/8">
                            <th class="text-left text-sm font-semibold text-notes-secondary px-4 py-3">Date</th>
                            <th class="text-left text-sm font-semibold text-notes-secondary px-4 py-3">Title</th>
                            <th class="text-left text-sm font-semibold text-notes-secondary px-4 py-3">Category</th>
                            <th class="text-left text-sm font-semibold text-notes-secondary px-4 py-3">Tags</th>
                            <th class="text-left text-sm font-semibold text-notes-secondary px-4 py-3">Paid By</th>
                            <th class="text-left text-sm font-semibold text-notes-secondary px-4 py-3">Payment</th>
                            <th class="text-right text-sm font-semibold text-notes-secondary px-4 py-3">Amount</th>
                            <th class="px-4 py-3" />
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr v-for="expense in expenses.data" :key="expense.id" class="hover:bg-white/2 transition-colors">
                            <td class="px-4 py-3 text-sm text-notes-secondary whitespace-nowrap">{{ formatDate(expense.created_at) }}</td>
                            <td class="px-4 py-3 text-base text-notes-text">{{ expense.title || '—' }}</td>
                            <td class="px-4 py-3 text-sm text-notes-secondary">{{ expense.category?.name }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="tag in expense.tags"
                                        :key="tag.id"
                                        class="text-xs px-1.5 py-0.5 rounded-full bg-white/5 text-notes-secondary"
                                    >
                                        {{ tag.name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-notes-secondary">{{ expense.paid_by }}</td>
                            <td class="px-4 py-3 text-sm text-notes-secondary">{{ expense.payment_method?.name }}</td>
                            <td class="px-4 py-3 text-right text-base font-semibold text-notes-text tabular-nums">{{ formatINR(expense.amount) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 justify-end">
                                    <Link :href="route('expenses.edit', expense.id)" class="text-sm font-medium text-notes-secondary hover:text-notes-text transition-colors">Edit</Link>
                                    <button @click="confirmDelete(expense)" class="text-sm font-medium text-red-400 hover:text-red-300 transition-colors">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <!-- Totals row -->
                    <tfoot>
                        <tr class="border-t border-white/8 bg-white/2">
                            <td colspan="6" class="px-4 py-3 text-sm font-semibold text-notes-secondary">
                                Total ({{ expenses.total }} expense{{ expenses.total === 1 ? '' : 's' }})
                            </td>
                            <td class="px-4 py-3 text-right text-base font-bold text-notes-text tabular-nums">
                                {{ formatINR(pageTotal) }}
                            </td>
                            <td />
                        </tr>
                    </tfoot>
                </table>

                <!-- Empty state -->
                <div v-else class="py-16 text-center">
                    <p class="text-base text-notes-secondary">No expenses found.</p>
                    <Link :href="route('expenses.create')" class="mt-3 inline-block text-base font-medium text-notes-yellow hover:opacity-80 transition-opacity">
                        Add your first expense →
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="expenses.last_page > 1" class="mt-4 flex items-center justify-between text-sm text-notes-secondary">
                <span>Page {{ expenses.current_page }} of {{ expenses.last_page }}</span>
                <div class="flex gap-2">
                    <Link
                        v-if="expenses.prev_page_url"
                        :href="expenses.prev_page_url"
                        class="px-3 py-1.5 rounded-lg border border-white/10 hover:bg-white/5 font-medium transition-colors"
                    >
                        Previous
                    </Link>
                    <Link
                        v-if="expenses.next_page_url"
                        :href="expenses.next_page_url"
                        class="px-3 py-1.5 rounded-lg border border-white/10 hover:bg-white/5 font-medium transition-colors"
                    >
                        Next
                    </Link>
                </div>
            </div>
        </div>

        <!-- Delete confirmation modal -->
        <div v-if="deletingExpense" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div class="bg-notes-elevated rounded-xl border border-white/8 p-6 max-w-sm w-full mx-4 shadow-xl">
                <p class="text-base text-notes-text font-semibold mb-1">Delete this expense?</p>
                <p class="text-sm text-notes-secondary mb-5">
                    {{ deletingExpense.title || formatINR(deletingExpense.amount) }} — it will be soft-deleted and can be recovered from the database.
                </p>
                <div class="flex gap-3 justify-end">
                    <button @click="deletingExpense = null" class="text-sm font-medium text-notes-secondary hover:text-notes-text px-3 py-1.5 rounded-lg hover:bg-white/5 transition-colors">
                        Cancel
                    </button>
                    <button @click="deleteExpense" class="text-sm font-semibold text-white bg-red-500 hover:bg-red-600 px-3 py-1.5 rounded-lg transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatINR } from '@/composables/useIndianCurrency.js';

const props = defineProps({
    expenses: Object,
    filters: Object,
    categories: Array,
    tags: Array,
    paymentMethods: Array,
});

const localFilters = ref({
    date_from: props.filters?.date_from ?? '',
    date_to: props.filters?.date_to ?? '',
    category_id: props.filters?.category_id ?? '',
    tag_ids: props.filters?.tag_ids ?? [],
    paid_by: props.filters?.paid_by ?? '',
    payment_method_id: props.filters?.payment_method_id ?? '',
});

function toggleTagFilter(id) {
    const ids = localFilters.value.tag_ids ?? [];
    const idx = ids.indexOf(id);
    if (idx === -1) {
        localFilters.value.tag_ids = [...ids, id];
    } else {
        localFilters.value.tag_ids = ids.filter((i) => i !== id);
    }
}

function applyFilters() {
    const params = {};
    for (const [key, val] of Object.entries(localFilters.value)) {
        if (val !== '' && val !== null && !(Array.isArray(val) && val.length === 0)) {
            params[key] = val;
        }
    }
    router.get(route('expenses.index'), params, { preserveState: true, replace: true });
}

function clearFilters() {
    localFilters.value = { date_from: '', date_to: '', category_id: '', tag_ids: [], paid_by: '', payment_method_id: '' };
    router.get(route('expenses.index'), {}, { preserveState: false });
}

const pageTotal = computed(() =>
    props.expenses.data.reduce((sum, e) => sum + parseFloat(e.amount), 0)
);

const deletingExpense = ref(null);

function confirmDelete(expense) {
    deletingExpense.value = expense;
}

function deleteExpense() {
    router.delete(route('expenses.destroy', deletingExpense.value.id), {
        onSuccess: () => { deletingExpense.value = null; },
        preserveScroll: true,
    });
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>
