<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="grid gap-4 lg:grid-cols-3">
                <div class="col-span-2 overflow-hidden bg-white shadow rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h3>
                    <p class="mt-1 text-sm text-gray-600">Your finance summary is ready. Track performance, budgets, and progress from one place.</p>
                    <div class="mt-4 flex flex-wrap gap-3 text-sm text-gray-700">
                        <span class="rounded-full bg-emerald-50 px-3 py-1">Savings rate: {{ $savingsRate }}%</span>
                        <span class="rounded-full bg-indigo-50 px-3 py-1">Financial score: {{ $financialScore }}/100</span>
                        <span class="rounded-full bg-amber-50 px-3 py-1">Net balance: ${{ number_format($netBalance, 2) }}</span>
                    </div>
                </div>
                <div class="overflow-hidden bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-900">Quick actions</h3>
                    <p class="mt-1 text-sm text-gray-600">Use the cards below to navigate expenses, incomes, budgets and goals faster.</p>
                    <a href="{{ route('reports.index') }}" class="mt-4 inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800">Open reports →</a>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                <div class="overflow-hidden bg-white shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-gray-500">Total Income</div>
                        <div class="mt-4 text-3xl font-semibold text-gray-900">${{ number_format($totalIncome, 2) }}</div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-gray-500">Total Expenses</div>
                        <div class="mt-4 text-3xl font-semibold text-gray-900">${{ number_format($totalExpenses, 2) }}</div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-gray-500">Budgets</div>
                        <div class="mt-4 text-3xl font-semibold text-gray-900">{{ $budgetCount }}</div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-gray-500">Goals</div>
                        <div class="mt-4 text-3xl font-semibold text-gray-900">{{ $goalCount }}</div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-gray-500">Net Balance</div>
                        <div class="mt-4 text-3xl font-semibold text-gray-900">${{ number_format($netBalance, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="col-span-2 overflow-hidden bg-white shadow rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Expense Efficiency</h3>
                        <p class="mt-2 text-sm text-gray-500">See how well your income covers your expenses.</p>

                        <div class="mt-6">
                            <div class="flex items-center justify-between text-sm font-medium text-gray-700">
                                <span>Expense rate</span>
                                <span>{{ $expenseRatio }}%</span>
                            </div>
                            <div class="mt-3 bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="h-3 rounded-full bg-indigo-600" style="width: {{ min($expenseRatio, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Investments</h3>
                        <p class="mt-2 text-sm text-gray-500">Open your investment overview and keep your money working for you.</p>
                        <div class="mt-6 text-3xl font-semibold text-gray-900">{{ $investmentCount }}</div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <a href="{{ route('expenses.index') }}" class="block bg-white shadow rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold text-gray-900">Expenses</h3>
                    <p class="mt-2 text-sm text-gray-500">Manage your spending categories and review recent expenses.</p>
                </a>
                <a href="{{ route('incomes.index') }}" class="block bg-white shadow rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold text-gray-900">Incomes</h3>
                    <p class="mt-2 text-sm text-gray-500">Track your income sources and add new entries in one place.</p>
                </a>
                <a href="{{ route('budgets.index') }}" class="block bg-white shadow rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold text-gray-900">Budgets</h3>
                    <p class="mt-2 text-sm text-gray-500">Control your savings targets and planned spending amounts.</p>
                </a>
                <a href="{{ route('chat.index') }}" class="block bg-white shadow rounded-lg p-6 hover:shadow-lg transition">
                    <h3 class="text-lg font-semibold text-gray-900">AI Assistant</h3>
                    <p class="mt-2 text-sm text-gray-500">Ask for quick insights or plan your next financial move.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
