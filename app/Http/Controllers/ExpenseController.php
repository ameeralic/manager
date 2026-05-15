<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseTag;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Expenses/Index', [
            'expenses' => [],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Expenses/Create', [
            'categories' => ExpenseCategory::orderBy('name')->get(['id', 'name']),
            'tags' => ExpenseTag::orderBy('name')->get(['id', 'name']),
            'paymentMethods' => PaymentMethod::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'expense_category_id' => ['required', 'integer', 'exists:expense_categories,id'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'paid_by' => ['required', 'in:Ameer,Jinsi'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:expense_tags,id'],
        ]);

        $expense = Expense::create([
            'title' => $validated['title'] ?? null,
            'expense_category_id' => $validated['expense_category_id'],
            'payment_method_id' => $validated['payment_method_id'],
            'paid_by' => $validated['paid_by'],
            'amount' => $validated['amount'],
        ]);

        $expense->tags()->sync($validated['tag_ids'] ?? []);

        return redirect()->route('expenses.index')->with('success', 'Expense saved.');
    }

    public function edit(Expense $expense): Response
    {
        return Inertia::render('Expenses/Edit', [
            'expense' => $expense->load(['category', 'paymentMethod', 'tags:id,name']),
            'categories' => ExpenseCategory::orderBy('name')->get(['id', 'name']),
            'tags' => ExpenseTag::orderBy('name')->get(['id', 'name']),
            'paymentMethods' => PaymentMethod::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'expense_category_id' => ['required', 'integer', 'exists:expense_categories,id'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'paid_by' => ['required', 'in:Ameer,Jinsi'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:expense_tags,id'],
        ]);

        $expense->update([
            'title' => $validated['title'] ?? null,
            'expense_category_id' => $validated['expense_category_id'],
            'payment_method_id' => $validated['payment_method_id'],
            'paid_by' => $validated['paid_by'],
            'amount' => $validated['amount'],
        ]);

        $expense->tags()->sync($validated['tag_ids'] ?? []);

        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('success', 'Expense deleted.');
    }
}
