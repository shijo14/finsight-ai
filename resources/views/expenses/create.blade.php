<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-neutral-900 dark:text-white leading-tight">Add Expense</h2>
            <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-4 py-2 btn-secondary">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-6">
                <form action="{{ route('expenses.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="category" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Category</label>
                        <input id="category" name="category" value="{{ old('category') }}" type="text" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                        @error('category')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Amount</label>
                        <input id="amount" name="amount" value="{{ old('amount') }}" type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                        @error('amount')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="expense_date" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Expense Date</label>
                        <input id="expense_date" name="expense_date" value="{{ old('expense_date') }}" type="date" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                        @error('expense_date')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Description</label>
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('expenses.index') }}" class="text-sm text-neutral-500 dark:text-slate-400 hover:text-neutral-900 dark:text-slate-100">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 btn-primary rounded-md">Save Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
