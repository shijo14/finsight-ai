<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestmentController extends Controller
{
    public function index(): View
    {
        $investments = auth()->user()->investments()->latest('investment_date')->get();

        return view('investments.index', compact('investments'));
    }

    public function create(): View
    {
        return view('investments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'investment_type' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'amount_invested' => ['required', 'numeric', 'min:0'],
            'current_value' => ['required', 'numeric', 'min:0'],
            'investment_date' => ['required', 'date'],
        ]);

        auth()->user()->investments()->create($validated);

        return redirect()->route('investments.index')->with('status', 'Investment saved successfully.');
    }

    public function show(Investment $investment): View
    {
        abort_unless(auth()->user()->is($investment->user), 403);

        return view('investments.show', compact('investment'));
    }

    public function edit(Investment $investment): View
    {
        abort_unless(auth()->user()->is($investment->user), 403);

        return view('investments.edit', compact('investment'));
    }

    public function update(Request $request, Investment $investment): RedirectResponse
    {
        abort_unless(auth()->user()->is($investment->user), 403);

        $validated = $request->validate([
            'investment_type' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'amount_invested' => ['required', 'numeric', 'min:0'],
            'current_value' => ['required', 'numeric', 'min:0'],
            'investment_date' => ['required', 'date'],
        ]);

        $investment->update($validated);

        return redirect()->route('investments.index')->with('status', 'Investment updated successfully.');
    }

    public function destroy(Investment $investment): RedirectResponse
    {
        abort_unless(auth()->user()->is($investment->user), 403);

        $investment->delete();

        return redirect()->route('investments.index')->with('status', 'Investment removed successfully.');
    }
}
