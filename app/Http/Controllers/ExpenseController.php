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
    public function index(Request $request): Response
    {
        $query = Expense::with(['category:id,name', 'paymentMethod:id,name', 'tags:id,name'])
            ->orderByDesc('created_at');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }
        if ($request->filled('tag_ids')) {
            foreach ($request->tag_ids as $tagId) {
                $query->whereHas('tags', fn ($q) => $q->where('expense_tags.id', $tagId));
            }
        }
        if ($request->filled('paid_by')) {
            $query->where('paid_by', $request->paid_by);
        }
        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        $expenses = $query->paginate(20)->withQueryString();

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses,
            'filters' => $request->only(['date_from', 'date_to', 'category_id', 'tag_ids', 'paid_by', 'payment_method_id']),
            'categories' => ExpenseCategory::orderBy('name')->get(['id', 'name']),
            'tags' => ExpenseTag::orderBy('name')->get(['id', 'name']),
            'paymentMethods' => PaymentMethod::orderBy('name')->get(['id', 'name']),
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
