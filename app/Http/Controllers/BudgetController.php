<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(): View
    {
        $budgets = auth()->user()->budgets()->latest('start_date')->get();

        return view('budgets.index', compact('budgets'));
    }

    public function create(): View
    {
        return view('budgets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string'],
        ]);

        auth()->user()->budgets()->create($validated);

        return redirect()->route('budgets.index')->with('status', 'Budget created successfully.');
    }

    public function show(Budget $budget): View
    {
        abort_unless(auth()->user()->is($budget->user), 403);

        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget): View
    {
        abort_unless(auth()->user()->is($budget->user), 403);

        return view('budgets.edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget): RedirectResponse
    {
        abort_unless(auth()->user()->is($budget->user), 403);

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string'],
        ]);

        $budget->update($validated);

        return redirect()->route('budgets.index')->with('status', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        abort_unless(auth()->user()->is($budget->user), 403);

        $budget->delete();

        return redirect()->route('budgets.index')->with('status', 'Budget removed successfully.');
    }
}
