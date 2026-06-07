<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900">Assistant</h3>
                <p class="mt-2 text-sm text-gray-500">Send a quick message and keep notes about your financial planning.</p>

                @if(session('status'))
                    <div class="mt-4 p-4 rounded-lg bg-green-50 text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('chat.store') }}" method="post" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea id="message" name="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ask about budgets, goals, or finance tasks."></textarea>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Send Message</button>
                </form>

                @if($history->isNotEmpty())
                    <div class="mt-8 space-y-4">
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Recent guidance</h4>
                        @foreach($history as $item)
                            <article class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <p class="text-sm text-gray-700"><strong>You:</strong> {{ $item->message }}</p>
                                <p class="mt-2 text-sm text-gray-600"><strong>Assistant:</strong> {{ $item->response }}</p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
