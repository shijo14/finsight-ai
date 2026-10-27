<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-neutral-900 dark:text-white leading-tight">Create Budget</h2>
            <a href="{{ route('budgets.index') }}" class="inline-flex items-center px-4 py-2 btn-secondary">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-6">
                <form action="{{ route('budgets.store') }}" method="POST" class="space-y-6">
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

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Start Date</label>
                            <input id="start_date" name="start_date" value="{{ old('start_date') }}" type="date" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                            @error('start_date')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">End Date</label>
                            <input id="end_date" name="end_date" value="{{ old('end_date') }}" type="date" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                            @error('end_date')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Notes</label>
                        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('budgets.index') }}" class="text-sm text-neutral-500 dark:text-slate-400 hover:text-neutral-900 dark:text-slate-100">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 btn-primary rounded-md">Save Budget</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
