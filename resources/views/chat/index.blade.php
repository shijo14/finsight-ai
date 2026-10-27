<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="section-label">AI-Powered</p>
            <h2 class="font-semibold text-xl text-neutral-900 dark:text-white leading-tight mt-1">
                {{ __('Your Personal Financial Advisor — Always On') }}
            </h2>
        </div>
    </x-slot>

    <style>
        /* Roast Mode Toggle */
        .roast-toggle-track {
            position: relative;
            display: inline-flex;
            align-items: center;
            width: 40px;
            height: 22px;
            border-radius: 9999px;
            background-color: #d1d5db;
            cursor: pointer;
            transition: background-color 0.2s ease;
            flex-shrink: 0;
        }
        .roast-toggle-track.active {
            background-color: #ef4444;
            box-shadow: 0 0 10px rgba(239,68,68,0.4);
        }
        .roast-toggle-thumb {
            position: absolute;
            left: 2px;
            width: 18px;
            height: 18px;
            border-radius: 9999px;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.25);
            transition: transform 0.2s ease;
        }
        .roast-toggle-track.active .roast-toggle-thumb {
            transform: translateX(18px);
        }
        .dark .roast-toggle-track {
            background-color: rgba(255,255,255,0.15);
        }
        .dark .roast-toggle-track.active {
            background-color: #ef4444;
        }
        /* Roast Mode active state — tint the chat header */
        .roast-mode-active .chat-header-bg {
            background: linear-gradient(90deg, rgba(239,68,68,0.08) 0%, transparent 60%);
        }
    </style>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Quick feature hints --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="glass-card p-4">
                    <h3 class="font-semibold text-cyan-600 dark:text-cyan-300 text-sm">Ask Anything</h3>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-slate-400">Natural language questions about your finances</p>
                </div>
                <div class="glass-card p-4 border-cyan-400/30">
                    <h3 class="font-semibold text-cyan-600 dark:text-cyan-300 text-sm">⚡ Take Action</h3>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-slate-400">
                        Try: <em>"Add Rs 500 petrol expense"</em> or <em>"Create Rs 10000 budget for food"</em>
                    </p>
                </div>
                <div class="glass-card p-4">
                    <h3 class="font-semibold text-cyan-600 dark:text-cyan-300 text-sm">Smart Insights</h3>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-slate-400">Proactive recommendations based on your data</p>
                </div>
            </div>

            {{-- Chat window --}}
            <div class="glass-card rounded-3xl overflow-hidden flex flex-col h-[72vh]">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-white/10 bg-neutral-100 dark:bg-white/5 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-cyan-500/20 border border-cyan-400/30 flex items-center justify-center animate-pulse-soft">
                            <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">FinSight AI Advisor</h3>
                            <p class="text-xs text-neutral-500 dark:text-slate-400">Online · Analyzing your workspace data</p>
                        </div>
                    </div>
                    
                    {{-- Roast Mode Toggle --}}
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-neutral-500 dark:text-slate-400">🔥 Roast Mode</span>
                        <div id="roast-toggle" class="roast-toggle-track" role="switch" aria-checked="false" title="Toggle Roast Mode">
                            <span class="roast-toggle-thumb"></span>
                        </div>
                    </div>
                </div>

                {{-- Messages container --}}
                <div id="chat-messages" class="flex-1 p-6 overflow-y-auto space-y-6 bg-neutral-50 dark:bg-white/[0.02]">

                    {{-- Welcome message --}}
                    <div class="flex justify-start chat-msg-bot">
                        <div class="max-w-[85%] rounded-2xl rounded-tl-sm px-5 py-4 glass-card text-neutral-700 dark:text-slate-300 text-sm leading-relaxed">
                            <p>Hello {{ auth()->user()->name }}! I'm your FinSight AI financial advisor — available 24/7.</p>
                            <p class="mt-2 text-neutral-500 dark:text-slate-400">I analyze your income, expenses, budgets, goals, and investments to deliver personalized guidance. You can also ask me to <strong class="text-cyan-600 dark:text-cyan-400">take actions</strong> — try typing:</p>
                            <ul class="mt-2 space-y-1 text-xs text-neutral-500 dark:text-slate-400">
                                <li>💸 <em>"Add Rs 1,500 petrol expense today"</em></li>
                                <li>💰 <em>"Record Rs 85,000 salary income"</em></li>
                                <li>📊 <em>"Create Rs 5,000 budget for dining"</em></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Past history --}}
                    @if($history->isNotEmpty())
                        @foreach($history as $item)
                            <div class="flex justify-end chat-msg-user">
                                <div class="max-w-[80%] rounded-2xl rounded-tr-sm px-5 py-3 bg-cyan-500 text-slate-950 text-sm font-medium shadow-lg shadow-cyan-500/20">
                                    <p>{{ $item->message }}</p>
                                </div>
                            </div>

                            <div class="flex justify-start chat-msg-bot">
                                <div class="max-w-[85%] rounded-2xl rounded-tl-sm px-5 py-4 glass-card text-neutral-700 dark:text-slate-300 text-sm leading-relaxed">
                                    <p>{!! nl2br(e($item->response)) !!}</p>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Typing indicator (hidden by default) --}}
                <div id="typing-indicator" class="hidden px-6 py-2">
                    <div class="flex items-center gap-2 text-xs text-neutral-400">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                        <span>FinSight AI is thinking...</span>
                    </div>
                </div>

                {{-- Input form --}}
                <div class="p-4 border-t border-neutral-200 dark:border-white/10 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md">
                    <form id="chat-form" class="flex gap-3">
                        @csrf
                        <input type="text" name="message" id="chat-input" required autocomplete="off"
                               class="glass-input flex-1"
                               placeholder="Ask about your finances or try 'Add Rs 500 petrol expense'...">
                        
                        <button type="button" id="voice-btn" class="p-3 rounded-xl bg-neutral-200 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 hover:bg-cyan-500 hover:text-white transition-colors" title="Voice Input">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                        </button>

                        <button type="submit" id="chat-submit" class="btn-primary px-6 py-3 flex items-center gap-2">
                            <span>Send</span>
                            <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const chatMessages  = document.getElementById('chat-messages');
        const chatForm      = document.getElementById('chat-form');
        const chatInput     = document.getElementById('chat-input');
        const chatSubmit    = document.getElementById('chat-submit');
        const typingIndicator = document.getElementById('typing-indicator');
        const roastToggle  = document.getElementById('roast-toggle');

        // Wire up roast toggle click
        roastToggle.addEventListener('click', () => {
            const isActive = roastToggle.classList.toggle('active');
            roastToggle.setAttribute('aria-checked', isActive);
        });

        // Scroll to bottom
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        scrollToBottom();

        function appendMessage(text, role) {
            const wrapper = document.createElement('div');
            wrapper.classList.add('flex', role === 'user' ? 'justify-end' : 'justify-start');

            const bubble = document.createElement('div');
            bubble.style.opacity = '0';
            bubble.style.transform = 'translateY(8px)';
            bubble.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

            if (role === 'user') {
                bubble.classList.add('max-w-[80%]', 'rounded-2xl', 'rounded-tr-sm', 'px-5', 'py-3',
                    'bg-cyan-500', 'text-slate-950', 'text-sm', 'font-medium', 'shadow-lg', 'shadow-cyan-500/20');
                bubble.innerHTML = `<p>${escapeHtml(text)}</p>`;
            } else {
                // Action confirmation badge detection
                const isAction = text.startsWith('✅');
                bubble.classList.add('max-w-[85%]', 'rounded-2xl', 'rounded-tl-sm', 'px-5', 'py-4',
                    'glass-card', 'text-neutral-700', 'dark:text-slate-300', 'text-sm', 'leading-relaxed');

                if (isAction) {
                    bubble.classList.add('border-emerald-400/40');
                    bubble.innerHTML = `<p class="text-emerald-600 dark:text-emerald-400 font-medium">${escapeHtml(text)}</p>`;
                } else {
                    bubble.innerHTML = `<p>${escapeHtml(text).replace(/\n/g, '<br>')}</p>`;
                }
            }

            wrapper.appendChild(bubble);
            chatMessages.appendChild(wrapper);

            // Animate in
            requestAnimationFrame(() => {
                bubble.style.opacity = '1';
                bubble.style.transform = 'translateY(0)';
            });

            scrollToBottom();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const message = chatInput.value.trim();
            if (!message) return;

            // Append user message
            appendMessage(message, 'user');
            chatInput.value = '';
            chatSubmit.disabled = true;

            // Show typing indicator
            typingIndicator.classList.remove('hidden');
            scrollToBottom();

            try {
                const csrfToken = document.querySelector('input[name="_token"]')?.value
                    || document.querySelector('meta[name="csrf-token"]')?.content;

                const roastMode = roastToggle.classList.contains('active');

                const res = await fetch('{{ route("chat.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ message, roast_mode: roastMode }),
                });

                const data = await res.json();

                typingIndicator.classList.add('hidden');

                if (data.success) {
                    appendMessage(data.response, 'bot');
                } else {
                    appendMessage('Sorry, something went wrong. Please try again.', 'bot');
                }
            } catch (err) {
                typingIndicator.classList.add('hidden');
                appendMessage('Connection error. Please check your network and try again.', 'bot');
            } finally {
                chatSubmit.disabled = false;
                chatInput.focus();
            }
        });

        // Voice API Logic
        const voiceBtn = document.getElementById('voice-btn');
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        
        if (SpeechRecognition) {
            const recognition = new SpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            
            recognition.onstart = () => {
                voiceBtn.classList.add('bg-red-500', 'text-white', 'animate-pulse');
                voiceBtn.classList.remove('bg-neutral-200', 'dark:bg-neutral-800', 'text-neutral-600', 'dark:text-neutral-400');
            };
            
            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                chatInput.value = transcript;
                chatForm.dispatchEvent(new Event('submit'));
            };
            
            recognition.onend = () => {
                voiceBtn.classList.remove('bg-red-500', 'text-white', 'animate-pulse');
                voiceBtn.classList.add('bg-neutral-200', 'dark:bg-neutral-800', 'text-neutral-600', 'dark:text-neutral-400');
            };
            
            voiceBtn.addEventListener('click', () => {
                recognition.start();
            });
        } else {
            voiceBtn.style.display = 'none'; // hide if not supported
        }
    </script>
</x-app-layout>
