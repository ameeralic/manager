<template>
    <AppLayout>
        <div class="max-w-xl mx-auto px-6 py-10">
            <div class="flex items-center gap-3 mb-8">
                <Link :href="route('expenses.index')" class="text-sm text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    ← Expenses
                </Link>
                <span class="text-gray-300 dark:text-gray-600">/</span>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Expense</h1>
            </div>

            <form @submit.prevent="submit">
                <ExpenseForm :form="form" :categories="categories" :paymentMethods="paymentMethods" />

                <div class="mt-8 flex gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex-1 py-3 rounded-xl bg-notes-yellow text-amber-900 font-semibold text-sm hover:opacity-90 active:scale-[.99] transition-all disabled:opacity-50"
                    >
                        Save Changes
                    </button>
                    <Link
                        :href="route('expenses.index')"
                        class="px-5 py-3 rounded-xl border border-black/10 dark:border-white/10 text-sm text-gray-600 dark:text-gray-400 hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
                    >
                        Cancel
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ExpenseForm from '@/Components/Expenses/ExpenseForm.vue';

const props = defineProps({
    expense: Object,
    categories: Array,
    paymentMethods: Array,
});

const form = useForm({
    title: props.expense.title ?? '',
    expense_category_id: props.expense.expense_category_id,
    payment_method_id: props.expense.payment_method_id,
    paid_by: props.expense.paid_by,
    amount: props.expense.amount,
    tag_ids: props.expense.tags.map((t) => t.id),
});

function submit() {
    form.put(route('expenses.update', props.expense.id));
}
</script>
