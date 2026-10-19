<!-- Floating Widgets Dock (Pojok Kanan Bawah): Wira Agent AI + WhatsApp Berkedip -->
<div class="fixed bottom-5 right-5 z-[90] flex flex-col items-end gap-3 select-none pointer-events-auto">

    <!-- 1. Floating Action Button: Wira Agent AI Chatbot -->
    <div class="relative group/wira flex items-center justify-end">
        <!-- Floating Tooltip Pill (Muncul saat hover di Desktop) -->
        <div class="hidden sm:flex items-center gap-2 mr-3 px-3.5 py-1.5 rounded-full bg-stone-900/95 text-white text-[11px] font-bold shadow-xl border border-red-500/30 backdrop-blur-md opacity-0 group-hover/wira:opacity-100 transition-all duration-200 pointer-events-none transform translate-x-2 group-hover/wira:translate-x-0 whitespace-nowrap">
            <span class="w-2 h-2 rounded-full bg-red-400 animate-ping"></span>
            <span>Tanya Wira Agent <strong class="text-amber-300 font-black">(AI PMR)</strong></span>
        </div>

        <!-- Button FAB Wira Agent -->
        <button type="button" onclick="toggleWiraAgentModal()" id="wira-agent-fab" class="relative w-13 h-13 sm:w-14 sm:h-14 rounded-full bg-gradient-to-tr from-stone-900 via-pmr-primary to-red-600 text-white shadow-2xl hover:shadow-red-900/50 flex items-center justify-center border-2 border-white/60 transition-all duration-300 transform hover:scale-108 active:scale-95 cursor-pointer group" aria-label="Buka Chat Wira Agent AI" title="Tanya Wira Agent (Asisten AI PMR)">
            <!-- Sparkle Ping -->
            <span class="absolute -inset-1 rounded-full bg-red-500 animate-ping opacity-30 pointer-events-none"></span>

            <!-- AI Floating Badge -->
            <span class="absolute -top-1 -right-1 bg-amber-400 text-stone-950 text-[9px] font-black px-1.5 py-0.2 rounded-full border-2 border-white shadow-md flex items-center gap-0.5">
                <i class="fa-solid fa-wand-magic-sparkles text-[8px]"></i>
                <span>AI</span>
            </span>

            <!-- Icons Toggle (Robot vs Close) -->
            <i id="wira-fab-open-icon" class="fa-solid fa-robot text-xl sm:text-2xl drop-shadow transition-transform group-hover:scale-110"></i>
            <i id="wira-fab-close-icon" class="fa-solid fa-xmark text-xl hidden"></i>
        </button>
    </div>

    <!-- 2. Floating Action Button: WhatsApp Widget dengan Animasi Berkedip -->
    <div class="relative group/wa flex items-center justify-end">
        <!-- Floating Tooltip Pill (Muncul saat hover & auto-attention) -->
        <div id="wa-floating-tooltip" class="hidden sm:flex items-center gap-2 mr-3 px-3.5 py-1.5 rounded-full bg-stone-900/95 text-white text-[11px] font-bold shadow-xl border border-emerald-500/40 backdrop-blur-md opacity-0 group-hover/wa:opacity-100 transition-all duration-200 pointer-events-none transform translate-x-2 group-hover/wa:translate-x-0 whitespace-nowrap">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span>Chat WhatsApp: <strong class="text-emerald-300 font-mono">0857-7024-6918</strong></span>
        </div>

        <!-- WhatsApp Circular Button dengan Animasi Berkedip & Radar Pulse -->
        <a href="https://wa.me/6285770246918?text=Halo%20PMR%20Wira%20SMAN%201%20Ciawi%2C%20saya%20ingin%20bertanya%20informasi%20seputar%20PMR%20dan%20layanan%20UKS..." target="_blank" rel="noopener noreferrer" class="relative w-13 h-13 sm:w-14 sm:h-14 rounded-full bg-gradient-to-tr from-emerald-600 to-[#25D366] text-white shadow-[0_0_20px_rgba(37,211,102,0.6)] hover:shadow-[0_0_30px_rgba(37,211,102,0.9)] flex items-center justify-center border-2 border-white/80 transition-all duration-300 transform hover:scale-110 active:scale-95 group wa-pulse-btn" aria-label="Hubungi WhatsApp PMR Wira SMAN 1 Ciawi" title="Chat WhatsApp Resmi (0857-7024-6918)">
            
            <!-- Radar Beacon Glowing Ping -->
            <span class="absolute -inset-1.5 rounded-full bg-emerald-400 animate-ping opacity-60 pointer-events-none"></span>

            <!-- Online Indicator Dot -->
            <span class="absolute top-0 right-0 w-3.5 h-3.5 rounded-full bg-emerald-300 border-2 border-white shadow-xs"></span>

            <!-- WhatsApp Icon -->
            <i class="fa-brands fa-whatsapp text-2xl sm:text-3xl drop-shadow-md transition-transform group-hover:scale-110"></i>
        </a>
    </div>

</div>

<!-- 3. Jendela Modal Chatbot AI: Wira Agent (~380px x 520px) -->
<div id="wira-agent-modal" class="fixed bottom-24 right-4 sm:right-6 z-[100] hidden flex-col w-[calc(100vw-32px)] sm:w-[395px] h-[535px] max-h-[82vh] bg-white rounded-3xl shadow-2xl border border-stone-200/90 overflow-hidden transition-all duration-300 animate-in fade-in zoom-in-95 font-sans">
    
    <!-- Modal Header -->
    <div class="relative bg-gradient-to-r from-stone-950 via-pmr-primary to-stone-900 text-white p-4 sm:p-4.5 flex items-center justify-between border-b border-white/10 shadow-md">
        <div class="flex items-center gap-3">
            <div class="relative w-10 h-10 rounded-2xl bg-white/15 p-1 border border-white/20 flex items-center justify-center text-white text-lg shadow-inner">
                <i class="fa-solid fa-robot text-red-300"></i>
                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-400 border-2 border-stone-900"></span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h4 class="font-black text-sm sm:text-base text-white tracking-tight">Wira Agent</h4>
                    <span class="bg-amber-400 text-stone-950 text-[9px] font-black px-2 py-0.2 rounded-full uppercase tracking-wider">AI PMR</span>
                </div>
                <p class="text-[10px] text-stone-300 font-medium flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Asisten Siaga • Ragana Dwi Pantara</span>
                </p>
            </div>
        </div>

        <!-- Header Action Buttons -->
        <div class="flex items-center gap-1.5">
            <button type="button" onclick="resetWiraAgentChat()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-stone-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Mulai Ulang Percakapan" aria-label="Mulai Ulang">
                <i class="fa-solid fa-rotate-right"></i>
            </button>
            <button type="button" onclick="toggleWiraAgentModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-stone-300 hover:text-white flex items-center justify-center text-sm transition cursor-pointer" title="Tutup Chat" aria-label="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Area Riwayat Chat (Scrollable) -->
    <div id="wira-chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3.5 bg-slate-50 text-xs sm:text-[13px] leading-relaxed custom-scrollbar">
        
        <!-- Welcome Message Balloon dari Wira Agent -->
        <div class="flex items-start gap-2.5 mr-auto max-w-[90%]">
            <div class="w-7 h-7 rounded-xl bg-red-100 text-pmr-primary flex items-center justify-center text-xs flex-shrink-0 mt-0.5 shadow-2xs font-bold border border-red-200">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div class="bg-white border border-stone-200/90 rounded-2xl rounded-tl-xs p-3.5 text-stone-800 shadow-xs space-y-2">
                <div class="text-[11px] font-bold text-pmr-primary flex items-center gap-1.5">
                    <span>Wira Agent</span>
                    <span class="text-[9px] text-stone-400 font-normal">Sekarang</span>
                </div>
                <p>
                    Halo Sahabat Kemanusiaan! 🌟 Saya <strong>Wira Agent</strong>, asisten virtual resmi PMR Wira SMAN 1 Ciawi.
                </p>
                <p class="text-stone-600">
                    Ada yang ingin ditanyakan seputar perlombaan PMR, aksi donor darah, pertolongan pertama (P3K), atau pendaftaran relawan?
                </p>
            </div>
        </div>

    </div>

    <!-- Typing Indicator ("Wira Agent sedang mengetik...") -->
    <div id="wira-typing-indicator" class="hidden px-4 py-2 bg-slate-50 border-t border-slate-100 items-center gap-2 text-stone-500 text-xs">
        <div class="w-5 h-5 rounded-lg bg-red-100 text-pmr-primary flex items-center justify-center text-[10px]">
            <i class="fa-solid fa-robot"></i>
        </div>
        <span class="font-medium">Wira Agent sedang mengetik</span>
        <div class="flex items-center gap-1 ml-1">
            <span class="w-1.5 h-1.5 bg-pmr-primary rounded-full animate-bounce [animation-delay:0ms]"></span>
            <span class="w-1.5 h-1.5 bg-pmr-primary rounded-full animate-bounce [animation-delay:150ms]"></span>
            <span class="w-1.5 h-1.5 bg-pmr-primary rounded-full animate-bounce [animation-delay:300ms]"></span>
        </div>
    </div>

    <!-- Quick Suggestion Chips (Tepat di atas Form Input) -->
    <div class="px-3.5 py-2 bg-white border-t border-slate-100 overflow-x-auto custom-scrollbar">
        <div class="flex items-center gap-1.5 whitespace-nowrap">
            <button type="button" onclick="sendWiraQuickMessage('🏆 Info Lomba PMR')" class="px-2.5 py-1 rounded-full bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-[11px] font-bold transition active:scale-95 cursor-pointer shadow-2xs">
                🏆 Info Lomba PMR
            </button>
            <button type="button" onclick="sendWiraQuickMessage('🩸 Jadwal Donor Darah')" class="px-2.5 py-1 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-200 text-[11px] font-bold transition active:scale-95 cursor-pointer shadow-2xs">
                🩸 Jadwal Donor Darah
            </button>
            <button type="button" onclick="sendWiraQuickMessage('🩹 Pertolongan Pertama (P3K)')" class="px-2.5 py-1 rounded-full bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-200 text-[11px] font-bold transition active:scale-95 cursor-pointer shadow-2xs">
                🩹 Pertolongan Pertama (P3K)
            </button>
            <button type="button" onclick="sendWiraQuickMessage('📋 Syarat Gabung Relawan')" class="px-2.5 py-1 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 text-[11px] font-bold transition active:scale-95 cursor-pointer shadow-2xs">
                📋 Syarat Gabung Relawan
            </button>
        </div>
    </div>

    <!-- Form Input Chat -->
    <div class="p-3 bg-white border-t border-stone-200/80">
        <form id="wira-agent-form" onsubmit="handleWiraFormSubmit(event)" class="flex items-center gap-2">
            <input type="text" id="wira-chat-input" placeholder="Tanyakan sesuatu ke Wira Agent..." class="flex-1 px-3.5 py-2.5 rounded-2xl bg-slate-100 focus:bg-white border border-transparent focus:border-pmr-primary focus:ring-2 focus:ring-red-100 text-xs sm:text-sm text-stone-800 transition outline-none" autocomplete="off" required>
            <button type="submit" id="wira-send-btn" class="w-10 h-10 rounded-2xl bg-pmr-primary hover:bg-red-800 text-white flex items-center justify-center transition active:scale-95 shadow-md flex-shrink-0 cursor-pointer" aria-label="Kirim Pesan">
                <i class="fa-solid fa-paper-plane text-xs"></i>
            </button>
        </form>
        <div class="flex items-center justify-between text-[9px] text-stone-400 mt-1.5 px-1">
            <span>Didukung AI Gemini & Knowledge PMR</span>
            <a href="https://wa.me/6285770246918" target="_blank" class="text-emerald-600 hover:underline font-bold flex items-center gap-1">
                <i class="fa-brands fa-whatsapp"></i> Chat Admin Langsung
            </a>
        </div>
    </div>

</div>

<!-- Styles Khusus Animasi Berkedip & Scrollbar Chat -->
<style>
    @keyframes waBlinkPulse {
        0%, 100% {
            box-shadow: 0 0 15px rgba(37, 211, 102, 0.7), 0 0 30px rgba(37, 211, 102, 0.3);
            transform: scale(1);
        }
        50% {
            box-shadow: 0 0 28px rgba(37, 211, 102, 0.95), 0 0 50px rgba(37, 211, 102, 0.5);
            transform: scale(1.04);
        }
    }

    .wa-pulse-btn {
        animation: waBlinkPulse 2.4s infinite ease-in-out;
    }

    /* Custom subtle scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.03);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(152, 0, 0, 0.25);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(152, 0, 0, 0.5);
    }
</style>

<!-- Script Logika Chat Wira Agent -->
<script>
    let wiraChatHistory = [];
    let wiraIsLoading = false;

    function toggleWiraAgentModal() {
        const modal = document.getElementById('wira-agent-modal');
        const openIcon = document.getElementById('wira-fab-open-icon');
        const closeIcon = document.getElementById('wira-fab-close-icon');
        const input = document.getElementById('wira-chat-input');

        if (!modal) return;

        const isHidden = modal.classList.contains('hidden');

        if (isHidden) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (openIcon) openIcon.classList.add('hidden');
            if (closeIcon) closeIcon.classList.remove('hidden');
            setTimeout(() => {
                if (input) input.focus();
                scrollWiraChatToBottom();
            }, 100);
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            if (openIcon) openIcon.classList.remove('hidden');
            if (closeIcon) closeIcon.classList.add('hidden');
        }
    }

    function scrollWiraChatToBottom() {
        const box = document.getElementById('wira-chat-messages');
        if (box) {
            box.scrollTop = box.scrollHeight;
        }
    }

    function sendWiraQuickMessage(text) {
        const input = document.getElementById('wira-chat-input');
        if (input) {
            input.value = text;
            handleWiraFormSubmit(new Event('submit'));
        }
    }

    function resetWiraAgentChat() {
        wiraChatHistory = [];
        const box = document.getElementById('wira-chat-messages');
        if (box) {
            box.innerHTML = `
                <div class="flex items-start gap-2.5 mr-auto max-w-[90%]">
                    <div class="w-7 h-7 rounded-xl bg-red-100 text-pmr-primary flex items-center justify-center text-xs flex-shrink-0 mt-0.5 shadow-2xs font-bold border border-red-200">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div class="bg-white border border-stone-200/90 rounded-2xl rounded-tl-xs p-3.5 text-stone-800 shadow-xs space-y-2">
                        <div class="text-[11px] font-bold text-pmr-primary flex items-center gap-1.5">
                            <span>Wira Agent</span>
                            <span class="text-[9px] text-stone-400 font-normal">Sesi Baru</span>
                        </div>
                        <p>
                            Percakapan telah direset. Halo kembali Sahabat Kemanusiaan! Ada yang bisa saya bantu seputar PMR Wira SMAN 1 Ciawi? 🌟
                        </p>
                    </div>
                </div>
            `;
        }
    }

    async function handleWiraFormSubmit(e) {
        if (e && e.preventDefault) e.preventDefault();
        if (wiraIsLoading) return;

        const input = document.getElementById('wira-chat-input');
        if (!input) return;

        const message = input.value.trim();
        if (!message) return;

        // Clear input
        input.value = '';

        // Tampilkan bubble pesan pengguna
        appendWiraMessage('user', message, getCurrentTime());

        // Tambahkan ke history
        wiraChatHistory.push({ role: 'user', content: message });

        // Tampilkan indikator mengetik
        setWiraTyping(true);

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const response = await fetch('/api/wira-agent/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    message: message,
                    history: wiraChatHistory.slice(-8) // Kirim 8 giliran terakhir untuk konteks optimal
                })
            });

            const data = await response.json();

            setWiraTyping(false);

            if (data.reply) {
                appendWiraMessage('assistant', data.reply, data.timestamp || getCurrentTime());
                wiraChatHistory.push({ role: 'assistant', content: data.reply });
            } else {
                appendWiraMessage('assistant', 'Mohon maaf, terjadi kendala saat memproses jawaban. Silakan tanyakan kembali atau hubungi WhatsApp kami ya! ❤️', getCurrentTime());
            }
        } catch (err) {
            console.error('Wira Agent network error:', err);
            setWiraTyping(false);
            appendWiraMessage('assistant', 'Jaringan sedang sibuk atau offline. Silakan coba sesaat lagi, atau hubungi langsung via WhatsApp di nomor 0857-7024-6918.', getCurrentTime());
        }
    }

    function setWiraTyping(isTyping) {
        wiraIsLoading = isTyping;
        const indicator = document.getElementById('wira-typing-indicator');
        const sendBtn = document.getElementById('wira-send-btn');

        if (indicator) {
            if (isTyping) {
                indicator.classList.remove('hidden');
                indicator.classList.add('flex');
            } else {
                indicator.classList.add('hidden');
                indicator.classList.remove('flex');
            }
        }

        if (sendBtn) {
            sendBtn.disabled = isTyping;
            sendBtn.classList.toggle('opacity-50', isTyping);
        }

        scrollWiraChatToBottom();
    }

    function appendWiraMessage(role, text, timeStr) {
        const box = document.getElementById('wira-chat-messages');
        if (!box) return;

        const isUser = (role === 'user');
        const div = document.createElement('div');

        if (isUser) {
            div.className = 'flex items-end justify-end ml-auto max-w-[85%]';
            div.innerHTML = `
                <div class="bg-gradient-to-r from-pmr-primary to-red-700 text-white rounded-2xl rounded-tr-xs p-3 shadow-xs space-y-1">
                    <p class="whitespace-pre-line text-white">${escapeWiraHtml(text)}</p>
                    <div class="text-[9px] text-red-200 text-right font-medium">${timeStr}</div>
                </div>
            `;
        } else {
            div.className = 'flex items-start gap-2.5 mr-auto max-w-[90%]';
            div.innerHTML = `
                <div class="w-7 h-7 rounded-xl bg-red-100 text-pmr-primary flex items-center justify-center text-xs flex-shrink-0 mt-0.5 shadow-2xs font-bold border border-red-200">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-white border border-stone-200/90 rounded-2xl rounded-tl-xs p-3.5 text-stone-800 shadow-xs space-y-1.5">
                    <div class="text-[11px] font-bold text-pmr-primary flex items-center gap-1.5">
                        <span>Wira Agent</span>
                        <span class="text-[9px] text-stone-400 font-normal">${timeStr}</span>
                    </div>
                    <div class="prose-xs leading-relaxed text-stone-700 space-y-1">
                        ${formatWiraMarkdown(text)}
                    </div>
                </div>
            `;
        }

        box.appendChild(div);
        scrollWiraChatToBottom();
    }

    function formatWiraMarkdown(text) {
        if (!text) return '';
        let escaped = escapeWiraHtml(text);

        // Bold: **text** -> <strong>text</strong>
        escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        // Italic: *text* -> <em>text</em>
        escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');

        // Bullet points: • or - at start of line
        escaped = escaped.replace(/^[•\-\*]\s+(.*)$/gm, '<li class="ml-4 list-disc">$1</li>');

        // Line breaks
        escaped = escaped.replace(/\n\n/g, '<div class="h-1.5"></div>');
        escaped = escaped.replace(/\n/g, '<br>');

        return escaped;
    }

    function escapeWiraHtml(str) {
        return (str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getCurrentTime() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        return `${h}:${m}`;
    }
</script>
