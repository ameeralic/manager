<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentMethodController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('PaymentMethods/Index', [
            'paymentMethods' => PaymentMethod::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:payment_methods,name'],
        ]);

        PaymentMethod::create($validated);

        return back()->with('success', 'Payment method created.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:payment_methods,name,'.$paymentMethod->id],
        ]);

        $paymentMethod->update($validated);

        return back()->with('success', 'Payment method updated.');
    }

    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        if ($paymentMethod->expenses()->exists()) {
            return back()->withErrors(['delete' => 'Cannot delete — this payment method is used by one or more expenses.']);
        }

        $paymentMethod->delete();

        return back()->with('success', 'Payment method deleted.');
    }
}
