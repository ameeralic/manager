<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\ExpenseTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseTagController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('ExpenseTags/Index', [
            'tags' => ExpenseTag::with('categories:id,name')
                ->withCount('expenses')
                ->orderBy('name')
                ->get(),
            'categories' => ExpenseCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:expense_tags,name'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:expense_categories,id'],
        ]);

        $tag = ExpenseTag::create(['name' => $validated['name']]);

        if (! empty($validated['category_ids'])) {
            $tag->categories()->sync($validated['category_ids']);
        }

        return back()->with('success', 'Tag created.');
    }

    public function update(Request $request, ExpenseTag $expenseTag): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:expense_tags,name,'.$expenseTag->id],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:expense_categories,id'],
        ]);

        $expenseTag->update(['name' => $validated['name']]);
        $expenseTag->categories()->sync($validated['category_ids'] ?? []);

        return back()->with('success', 'Tag updated.');
    }

    public function destroy(ExpenseTag $expenseTag): RedirectResponse
    {
        if ($expenseTag->expenses()->exists()) {
            return back()->withErrors(['delete' => 'Cannot delete — this tag is used by one or more expenses.']);
        }

        $expenseTag->delete();

        return back()->with('success', 'Tag deleted.');
    }
}
