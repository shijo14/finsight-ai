<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-neutral-900 dark:text-white leading-tight">Income Details</h2>
            <a href="{{ route('incomes.index') }}" class="inline-flex items-center px-4 py-2 btn-secondary">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-6">
                <dl class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-neutral-500 dark:text-slate-400">Source</dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-slate-100">{{ $income->source }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-neutral-500 dark:text-slate-400">Amount</dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-slate-100">{{ format_money($income->amount, 2) }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-neutral-500 dark:text-slate-400">Date</dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-slate-100">{{ $income->income_date->format('M j, Y') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex items-center gap-3">
                    <a href="{{ route('incomes.edit', $income) }}" class="inline-flex items-center px-4 py-2 btn-primary rounded-md">Edit</a>
                    <form action="{{ route('incomes.destroy', $income) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600/80 text-neutral-900 dark:text-white rounded-xl hover:bg-red-500 border border-red-500/30" onclick="return confirm('Delete this income?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
