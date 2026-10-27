<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-neutral-900 dark:text-white leading-tight">Expenses</h2>
            <a href="{{ route('expenses.create') }}" class="inline-flex items-center px-4 py-2 btn-primary rounded-md shadow-sm">New Expense</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-6 rounded-lg bg-neutral-200 dark:bg-white/10 p-4 text-neutral-700 dark:text-slate-300">{{ session('status') }}</div>
            @endif

            <div class="overflow-hidden glass-card overflow-hidden">
                <div class="p-6">
                    @if($expenses->isEmpty())
                        <div class="rounded-lg border border-dashed border-neutral-300 dark:border-white/20 p-8 text-center">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-slate-100">No expenses recorded</h3>
                            <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Track your spending by adding expense entries.</p>
                            <a href="{{ route('expenses.create') }}" class="mt-4 inline-flex items-center px-4 py-2 btn-primary rounded-md">Add Expense</a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-neutral-200 dark:divide-white/10">
                                <thead class="bg-neutral-100 dark:bg-white/5">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-200 dark:divide-white/10">
                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-slate-100">{{ $expense->category }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-slate-100">{{ format_money($expense->amount, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-slate-100">{{ $expense->expense_date->format('M j, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                <a href="{{ route('expenses.edit', $expense) }}" class="text-neutral-900 dark:text-slate-100 hover:text-neutral-700 dark:text-slate-300">Edit</a>
                                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-neutral-500 dark:text-slate-400 hover:text-neutral-700 dark:text-slate-300" onclick="return confirm('Delete this expense?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
