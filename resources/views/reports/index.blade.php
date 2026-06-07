<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900">Financial reports</h3>
                <p class="mt-2 text-sm text-gray-500">A quick snapshot of how your money is flowing this month.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500">Total income</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900">${{ number_format($totalIncome, 2) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500">Total expenses</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900">${{ number_format($totalExpenses, 2) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500">Net balance</p>
                    <p class="mt-3 text-3xl font-semibold text-emerald-600">${{ number_format($netBalance, 2) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500">Savings rate</p>
                    <p class="mt-3 text-3xl font-semibold text-indigo-600">{{ $savingsRate }}%</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h4 class="text-base font-semibold text-gray-900">What this means</h4>
                    <p class="mt-3 text-sm text-gray-600">Your expense ratio is {{ $expenseRatio }}% and you have {{ $budgetCount }} budgets, {{ $goalCount }} goals, and {{ $investmentCount }} investment entries to monitor. Keep your savings rate positive to build long-term resilience.</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h4 class="text-base font-semibold text-gray-900">Advice</h4>
                    <ul class="mt-3 space-y-2 text-sm text-gray-600">
                        <li>• Review discretionary spending weekly.</li>
                        <li>• Increase contributions to your strongest goals.</li>
                        <li>• Keep your expense ratio below 80% for healthier cash flow.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
