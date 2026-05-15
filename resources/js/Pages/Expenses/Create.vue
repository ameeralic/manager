<template>
    <AppLayout>
        <div class="max-w-xl mx-auto px-6 py-10">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-8">Add Expense</h1>

            <form @submit.prevent="submit">
                <ExpenseForm :form="form" :categories="categories" :paymentMethods="paymentMethods" />

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="mt-8 w-full py-3 rounded-xl bg-notes-yellow text-amber-900 font-semibold text-sm hover:opacity-90 active:scale-[.99] transition-all disabled:opacity-50"
                >
                    Save Expense
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ExpenseForm from '@/Components/Expenses/ExpenseForm.vue';

defineProps({
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

function submit() {
    form.post(route('expenses.store'));
}
</script>
