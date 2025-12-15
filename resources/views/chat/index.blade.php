<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Curhat App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top, #1f2937, #020617);
        }

        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.25);
            border-radius: 10px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center text-white">

<div class="glass w-full max-w-md h-[85vh] rounded-3xl flex flex-col shadow-2xl">

    {{-- HEADER --}}
    <div class="px-5 py-4 border-b border-white/10 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">💬</div>
        <div>
            <h1 class="font-semibold">Teman Curhat</h1>
            <p class="text-xs text-white/60">AI yang selalu mendengarkan</p>
        </div>
    </div>

    {{-- CHAT AREA --}}
    <div id="chatBox" class="flex-1 overflow-y-auto px-4 py-4 space-y-3"></div>

    {{-- INPUT --}}
    <div class="p-4 border-t border-white/10">
        <div class="flex gap-2">
            <input id="messageInput"
                   type="text"
                   placeholder="Ceritakan isi hatimu…"
                   class="flex-1 bg-white/10 rounded-full px-4 py-2 outline-none placeholder:text-white/50 focus:ring-2 focus:ring-white/30"/>

            <button onclick="sendMessage()"
                    class="px-4 py-2 rounded-full bg-white/20 hover:bg-white/30 transition">
                ➤
            </button>
        </div>
    </div>
</div>

<script>
    const API_URL = "{{ url('/api/curhat') }}";
    let curhatId = null;

    const chatBox = document.getElementById('chatBox');
    const input = document.getElementById('messageInput');

    function addMessage(text, sender = 'user') {
        const wrap = document.createElement('div');
        wrap.className = sender === 'user' ? 'flex justify-end' : 'flex justify-start';

        const bubble = document.createElement('div');
        bubble.className = `
            px-4 py-2 rounded-2xl max-w-[80%] text-sm leading-relaxed
            ${sender === 'user'
                ? 'bg-blue-500/40 rounded-br-none'
                : 'bg-white/20 rounded-bl-none'}
        `;
        bubble.innerText = text;

        wrap.appendChild(bubble);
        chatBox.appendChild(wrap);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        addMessage(text, 'user');
        input.value = '';

        // loading bubble
        const loading = document.createElement('div');
        loading.className = 'text-white/50 text-xs';
        loading.innerText = 'AI sedang mengetik...';
        chatBox.appendChild(loading);

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    message: text,
                    anonymous: true
                })
            });

            const data = await response.json();
            chatBox.removeChild(loading);

            const aiMessage = data.curhat.messages.find(m => m.sender === 'ai');
            if (aiMessage) {
                addMessage(aiMessage.message, 'ai');
            } else {
                addMessage('Maaf, aku belum bisa merespon.', 'ai');
            }

        } catch (err) {
            chatBox.removeChild(loading);
            addMessage('Terjadi kesalahan. Coba lagi ya.', 'ai');
        }
    }

    input.addEventListener('keypress', e => {
        if (e.key === 'Enter') sendMessage();
    });
</script>

</body>
</html>