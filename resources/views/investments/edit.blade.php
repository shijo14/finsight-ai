<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-neutral-900 dark:text-white leading-tight">Edit Investment</h2>
            <a href="{{ route('investments.index') }}" class="inline-flex items-center px-4 py-2 btn-secondary">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-6">
                <form action="{{ route('investments.update', $investment) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="investment_type" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Investment Type</label>
                        <input id="investment_type" name="investment_type" value="{{ old('investment_type', $investment->investment_type) }}" type="text" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                        @error('investment_type')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Name</label>
                        <input id="name" name="name" value="{{ old('name', $investment->name) }}" type="text" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                        @error('name')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="amount_invested" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Amount Invested</label>
                            <input id="amount_invested" name="amount_invested" value="{{ old('amount_invested', $investment->amount_invested) }}" type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                            @error('amount_invested')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="current_value" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Current Value</label>
                            <input id="current_value" name="current_value" value="{{ old('current_value', $investment->current_value) }}" type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                            @error('current_value')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="investment_date" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Investment Date</label>
                        <input id="investment_date" name="investment_date" value="{{ old('investment_date', $investment->investment_date->format('Y-m-d')) }}" type="date" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                        @error('investment_date')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('investments.index') }}" class="text-sm text-neutral-500 dark:text-slate-400 hover:text-neutral-900 dark:text-slate-100">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 btn-primary rounded-md">Update Investment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
