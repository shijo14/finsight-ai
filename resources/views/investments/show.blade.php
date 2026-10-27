<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-neutral-900 dark:text-white leading-tight">Investment Details</h2>
            <a href="{{ route('investments.index') }}" class="inline-flex items-center px-4 py-2 btn-secondary">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-6">
                <dl class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-neutral-500 dark:text-slate-400">Type</dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-slate-100">{{ $investment->investment_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-neutral-500 dark:text-slate-400">Name</dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-slate-100">{{ $investment->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-neutral-500 dark:text-slate-400">Amount Invested</dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-slate-100">{{ format_money($investment->amount_invested, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-neutral-500 dark:text-slate-400">Current Value</dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-slate-100">{{ format_money($investment->current_value, 2) }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-neutral-500 dark:text-slate-400">Investment Date</dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-slate-100">{{ $investment->investment_date->format('M j, Y') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex items-center gap-3">
                    <a href="{{ route('investments.edit', $investment) }}" class="inline-flex items-center px-4 py-2 btn-primary rounded-md">Edit</a>
                    <form action="{{ route('investments.destroy', $investment) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600/80 text-neutral-900 dark:text-white rounded-xl hover:bg-red-500 border border-red-500/30" onclick="return confirm('Delete this investment?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
