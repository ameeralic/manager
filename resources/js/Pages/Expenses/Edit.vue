<template>
    <AppLayout>
        <div class="max-w-xl mx-auto px-6 py-10">
            <div class="flex items-center gap-3 mb-8">
                <Link :href="route('expenses.index')" class="text-sm font-medium text-notes-secondary hover:text-notes-text transition-colors">
                    ← Expenses
                </Link>
                <span class="text-notes-secondary/30">/</span>
                <h1 class="text-2xl font-bold text-notes-text">Edit Expense</h1>
            </div>

            <form @submit.prevent="submit">
                <ExpenseForm :form="form" :categories="categories" :tags="tags" :paymentMethods="paymentMethods" />

                <div class="mt-8 flex gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex-1 py-3 rounded-xl bg-notes-yellow text-amber-900 font-bold text-base hover:opacity-90 active:scale-[.99] transition-all disabled:opacity-50"
                    >
                        Save Changes
                    </button>
                    <Link
                        :href="route('expenses.index')"
                        class="px-5 py-3 rounded-xl border border-white/10 text-base font-medium text-notes-secondary hover:bg-white/5 transition-colors"
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
    tags: Array,
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
