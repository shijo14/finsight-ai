<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function index(): View
    {
        $incomes = auth()->user()->incomes()->latest('income_date')->get();

        return view('incomes.index', compact('incomes'));
    }

    public function create(): View
    {
        return view('incomes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'income_date' => ['required', 'date'],
        ]);

        auth()->user()->incomes()->create($validated);

        return redirect()->route('incomes.index')->with('status', 'Income added successfully.');
    }

    public function show(Income $income): View
    {
        abort_unless(auth()->user()->is($income->user), 403);

        return view('incomes.show', compact('income'));
    }

    public function edit(Income $income): View
    {
        abort_unless(auth()->user()->is($income->user), 403);

        return view('incomes.edit', compact('income'));
    }

    public function update(Request $request, Income $income): RedirectResponse
    {
        abort_unless(auth()->user()->is($income->user), 403);

        $validated = $request->validate([
            'source' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'income_date' => ['required', 'date'],
        ]);

        $income->update($validated);

        return redirect()->route('incomes.index')->with('status', 'Income updated successfully.');
    }

    public function destroy(Income $income): RedirectResponse
    {
        abort_unless(auth()->user()->is($income->user), 403);

        $income->delete();

        return redirect()->route('incomes.index')->with('status', 'Income removed successfully.');
    }
}
