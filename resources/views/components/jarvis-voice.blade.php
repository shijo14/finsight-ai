<div x-data="jarvisVoice()" 
     x-init="initJarvis()"
     class="fixed bottom-6 right-6 z-50">

    {{-- Floating Glowing Trigger Button --}}
    <button @click="toggleJarvis()"
            type="button"
            :class="isListening ? 'ring-4 ring-cyan-400 ring-offset-2 animate-pulse bg-cyan-500 text-white' : 'bg-gradient-to-r from-slate-900 via-cyan-950 to-slate-900 border border-cyan-400/40 text-cyan-400 shadow-xl shadow-cyan-500/20 hover:scale-105'"
            class="group relative flex items-center gap-3 px-4 py-3 rounded-full transition-all duration-300">
        
        {{-- Pulsing Orb --}}
        <div class="relative flex items-center justify-center">
            <span class="absolute w-6 h-6 rounded-full bg-cyan-400/30 animate-ping" x-show="isListening || isSpeaking"></span>
            <span class="relative w-3.5 h-3.5 rounded-full bg-cyan-400 shadow-lg shadow-cyan-400/80"></span>
        </div>

        <div class="text-left">
            <div class="text-[11px] font-bold tracking-wider uppercase text-cyan-400 group-hover:text-cyan-300 flex items-center gap-1.5">
                <span>JARVIS CFO</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            </div>
            <div class="text-[10px] text-slate-400" x-text="statusLabel">Voice Ready</div>
        </div>

        <svg class="w-5 h-5 text-cyan-400 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
        </svg>
    </button>

    {{-- Jarvis Interactive Voice Console Modal --}}
    <div x-show="isOpen" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-95"
         class="fixed bottom-24 right-6 w-96 max-w-[calc(100vw-3rem)] rounded-3xl bg-slate-950/95 backdrop-blur-2xl border border-cyan-500/30 shadow-2xl shadow-cyan-950/80 p-5 overflow-hidden text-slate-100 z-50">

        {{-- Ambient Neon Glow --}}
        <div class="absolute -top-16 -right-16 w-44 h-44 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-44 h-44 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Header --}}
        <div class="flex items-center justify-between pb-3 border-b border-white/10 relative z-10">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-ping"></div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-cyan-300">JARVIS CFO • Audio Telemetry</h4>
            </div>
            <button @click="isOpen = false; stopListening(); stopSpeaking();" class="text-slate-400 hover:text-white p-1 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- Central Audio Soundwave & Orb Visualizer --}}
        <div class="py-6 flex flex-col items-center justify-center relative z-10">
            <div class="relative w-24 h-24 flex items-center justify-center cursor-pointer" @click="toggleMic()">
                {{-- Outer Pulse Rings --}}
                <div :class="isListening ? 'scale-125 opacity-40 bg-cyan-500' : (isSpeaking ? 'scale-125 opacity-40 bg-emerald-500' : 'opacity-10 bg-slate-700')"
                     class="absolute inset-0 rounded-full transition-all duration-700 animate-ping"></div>
                <div :class="isListening ? 'border-cyan-400 shadow-cyan-500/50' : (isSpeaking ? 'border-emerald-400 shadow-emerald-500/50' : 'border-slate-700')"
                     class="w-20 h-20 rounded-full border-2 bg-gradient-to-tr from-slate-900 to-slate-800 flex items-center justify-center shadow-xl transition-all">
                    
                    <svg x-show="!isLoading" 
                         :class="isListening ? 'text-cyan-400 animate-bounce' : (isSpeaking ? 'text-emerald-400 animate-pulse' : 'text-slate-400')"
                         class="w-8 h-8 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>

                    {{-- Loading Spinner --}}
                    <div x-show="isLoading" class="animate-spin rounded-full h-7 w-7 border-2 border-cyan-400 border-t-transparent"></div>
                </div>
            </div>

            {{-- Soundwave Canvas or Bars --}}
            <div class="flex items-center gap-1.5 mt-4 h-6">
                <template x-for="i in 9" :key="i">
                    <span :class="isListening ? 'bg-cyan-400' : (isSpeaking ? 'bg-emerald-400' : 'bg-slate-700')"
                          :style="getBarStyle(i)"
                          class="w-1 rounded-full transition-all duration-150 inline-block"></span>
                </template>
            </div>

            <p class="text-[11px] font-medium tracking-wide mt-2" 
               :class="isListening ? 'text-cyan-400 animate-pulse' : (isSpeaking ? 'text-emerald-400' : (isLoading ? 'text-amber-400' : 'text-slate-400'))"
               x-text="statusText">Tap orb or speak</p>
        </div>

        {{-- Spoken Transcript / Output Bubble --}}
        <div class="space-y-2 max-h-40 overflow-y-auto pr-1 text-xs relative z-10">
            {{-- User spoken transcript --}}
            <div x-show="transcript" class="bg-white/5 border border-white/10 rounded-xl p-2.5 text-slate-300">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">You said:</span>
                <p x-text="transcript" class="italic"></p>
            </div>

            {{-- Jarvis response --}}
            <div x-show="jarvisResponse" class="bg-cyan-950/40 border border-cyan-500/30 rounded-xl p-3 text-cyan-100 shadow-inner">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] uppercase font-bold text-cyan-400 flex items-center gap-1">
                        <span>🎙️</span> JARVIS Verdict:
                    </span>
                    <button @click="replayVoice()" title="Replay voice" class="text-cyan-400 hover:text-white text-xs">
                        🔊
                    </button>
                </div>
                <p x-text="jarvisResponse" class="leading-relaxed"></p>
            </div>
        </div>

        {{-- Voice Prompt Chips --}}
        <div class="mt-4 pt-3 border-t border-white/10 relative z-10">
            <span class="text-[10px] uppercase tracking-wider text-slate-500 block mb-2 font-semibold">Try asking Jarvis:</span>
            <div class="flex flex-wrap gap-1.5">
                <button @click="sendPrompt('Can I afford a Rs 3500 dinner tonight?')" 
                        class="text-[11px] bg-white/5 hover:bg-cyan-500/20 hover:border-cyan-400/40 border border-white/10 rounded-lg px-2.5 py-1 text-slate-300 transition-colors text-left">
                    "Can I afford Rs 3,500 dinner?"
                </button>
                <button @click="sendPrompt('What is my runway if I lose my job?')" 
                        class="text-[11px] bg-white/5 hover:bg-cyan-500/20 hover:border-cyan-400/40 border border-white/10 rounded-lg px-2.5 py-1 text-slate-300 transition-colors text-left">
                    "What is my survival runway?"
                </button>
                <button @click="sendPrompt('How much cash balance do I have left?')" 
                        class="text-[11px] bg-white/5 hover:bg-cyan-500/20 hover:border-cyan-400/40 border border-white/10 rounded-lg px-2.5 py-1 text-slate-300 transition-colors text-left">
                    "What is my current balance?"
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function jarvisVoice() {
    return {
        isOpen: false,
        isListening: false,
        isSpeaking: false,
        isLoading: false,
        statusLabel: 'Voice Ready',
        statusText: 'Tap orb or speak',
        transcript: '',
        jarvisResponse: '',
        recognition: null,
        soundwaveInterval: null,
        barHeights: [6, 12, 18, 24, 18, 14, 22, 12, 8],

        initJarvis() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (SpeechRecognition) {
                this.recognition = new SpeechRecognition();
                this.recognition.continuous = false;
                this.recognition.interimResults = true;
                this.recognition.lang = 'en-IN';

                this.recognition.onstart = () => {
                    this.isListening = true;
                    this.statusLabel = 'Listening...';
                    this.statusText = 'Listening to your voice...';
                    this.startSoundwave();
                };

                this.recognition.onresult = (event) => {
                    let interim = '';
                    for (let i = event.resultIndex; i < event.results.length; ++i) {
                        interim += event.results[i][0].transcript;
                    }
                    this.transcript = interim;
                };

                this.recognition.onerror = (event) => {
                    this.isListening = false;
                    this.stopSoundwave();
                    this.statusLabel = 'Voice Ready';
                    this.statusText = 'Mic error: ' + (event.error || 'Click a prompt below');
                };

                this.recognition.onend = () => {
                    this.isListening = false;
                    this.stopSoundwave();
                    if (this.transcript.trim()) {
                        this.processVoiceQuery(this.transcript);
                    } else {
                        this.statusLabel = 'Voice Ready';
                        this.statusText = 'Tap orb or speak';
                    }
                };
            } else {
                this.statusLabel = 'Text Mode';
                this.statusText = 'Web Speech not supported on this browser';
            }
        },

        toggleJarvis() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && !this.isListening && !this.jarvisResponse) {
                this.startListening();
            }
        },

        toggleMic() {
            if (this.isListening) {
                this.stopListening();
            } else {
                this.startListening();
            }
        },

        startListening() {
            this.stopSpeaking();
            if (this.recognition) {
                try {
                    this.transcript = '';
                    this.recognition.start();
                } catch (e) {
                    // already started
                }
            } else {
                this.statusText = 'Voice not supported. Click a prompt below!';
            }
        },

        stopListening() {
            if (this.recognition && this.isListening) {
                try { this.recognition.stop(); } catch (e) {}
            }
            this.isListening = false;
            this.stopSoundwave();
        },

        startSoundwave() {
            clearInterval(this.soundwaveInterval);
            this.soundwaveInterval = setInterval(() => {
                this.barHeights = this.barHeights.map(() => Math.floor(Math.random() * 20) + 4);
            }, 120);
        },

        stopSoundwave() {
            clearInterval(this.soundwaveInterval);
            this.barHeights = [6, 10, 14, 18, 14, 10, 14, 10, 6];
        },

        getBarStyle(i) {
            const h = (this.isListening || this.isSpeaking) ? (this.barHeights[i - 1] || 10) : 6;
            return `height: ${h}px;`;
        },

        sendPrompt(text) {
            this.transcript = text;
            this.processVoiceQuery(text);
        },

        async processVoiceQuery(query) {
            this.isLoading = true;
            this.statusLabel = 'Analyzing...';
            this.statusText = 'JARVIS calculating telemetry...';
            this.stopListening();

            try {
                const res = await fetch('{{ route('jarvis.query') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ transcript: query })
                });

                const data = await res.json();
                if (data.speech_text) {
                    this.jarvisResponse = data.display_text;
                    this.statusLabel = 'Speaking...';
                    this.statusText = 'JARVIS responding';
                    this.speakAloud(data.speech_text);
                } else {
                    this.jarvisResponse = 'Telemetry processed. Balance: Rs ' + (data.net_balance || 0);
                    this.statusText = 'Ready';
                }
            } catch (err) {
                this.jarvisResponse = 'Connection timeout. Cash telemetry is currently offline.';
                this.statusText = 'Offline';
            } finally {
                this.isLoading = false;
            }
        },

        speakAloud(text) {
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.rate = 1.05;
            utterance.pitch = 0.95;

            // Pick a good voice if available
            const voices = window.speechSynthesis.getVoices();
            const preferredVoice = voices.find(v => v.name.includes('Google UK English Male') || v.name.includes('Daniel') || v.name.includes('en-IN') || v.lang.startsWith('en'));
            if (preferredVoice) {
                utterance.voice = preferredVoice;
            }

            utterance.onstart = () => {
                this.isSpeaking = true;
                this.startSoundwave();
            };

            utterance.onend = () => {
                this.isSpeaking = false;
                this.stopSoundwave();
                this.statusLabel = 'Voice Ready';
                this.statusText = 'Tap orb or speak';
            };

            utterance.onerror = () => {
                this.isSpeaking = false;
                this.stopSoundwave();
                this.statusLabel = 'Voice Ready';
                this.statusText = 'Audio playback completed';
            };

            window.speechSynthesis.speak(utterance);
        },

        replayVoice() {
            if (this.jarvisResponse) {
                this.speakAloud(this.jarvisResponse);
            }
        },

        stopSpeaking() {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }
            this.isSpeaking = false;
            this.stopSoundwave();
        }
    };
}
</script>
