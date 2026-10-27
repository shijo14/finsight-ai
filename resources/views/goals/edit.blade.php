<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-neutral-900 dark:text-white leading-tight">Edit Goal</h2>
            <a href="{{ route('goals.index') }}" class="inline-flex items-center px-4 py-2 btn-secondary">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-6">
                <form action="{{ route('goals.update', $goal) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="title" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Title</label>
                        <input id="title" name="title" value="{{ old('title', $goal->title) }}" type="text" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                        @error('title')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="target_amount" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Target Amount</label>
                        <input id="target_amount" name="target_amount" value="{{ old('target_amount', $goal->target_amount) }}" type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                        @error('target_amount')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="current_amount" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Current Amount</label>
                        <input id="current_amount" name="current_amount" value="{{ old('current_amount', $goal->current_amount) }}" type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50">
                        @error('current_amount')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Deadline</label>
                            <input id="deadline" name="deadline" value="{{ old('deadline', optional($goal->deadline)->format('Y-m-d')) }}" type="date" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50">
                            @error('deadline')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-neutral-700 dark:text-slate-300">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-neutral-300 dark:border-white/15 shadow-sm focus:border-cyan-400/50 focus:ring-cyan-400/50" required>
                                <option value="pending" {{ old('status', $goal->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ old('status', $goal->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $goal->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')<p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('goals.index') }}" class="text-sm text-neutral-500 dark:text-slate-400 hover:text-neutral-900 dark:text-slate-100">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 btn-primary rounded-md">Update Goal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
