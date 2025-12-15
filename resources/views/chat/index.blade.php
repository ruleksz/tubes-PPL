<!-- resources/views/curhat/chat.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Curhat App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top, #1f2937, #020617);
        }
        .glass {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,0.15);
        }
        ::-webkit-scrollbar{width:6px}
        ::-webkit-scrollbar-thumb{background:rgba(255,255,255,.25);border-radius:10px}
    </style>
</head>
<body class="h-screen text-white">

<div class="flex h-full">

    <!-- ================= SIDEBAR (CHAT HISTORY) ================= -->
    <aside class="glass w-72 p-4 hidden md:flex flex-col">
        <div class="mb-4">
            <h2 class="font-semibold text-lg">Riwayat Curhat</h2>
            <p class="text-xs text-white/60">Sesi sebelumnya</p>
        </div>

        <div id="historyList" class="flex-1 overflow-y-auto space-y-2"></div>

        <button onclick="newSession()"
                class="mt-3 w-full py-2 rounded-xl bg-white/15 hover:bg-white/25 transition text-sm">
            + Curhat Baru
        </button>
    </aside>

    <!-- ================= CHAT AREA ================= -->
    <main class="flex-1 flex items-center justify-center">
        <div class="glass w-full max-w-md h-[85vh] rounded-3xl flex flex-col shadow-2xl">

            <!-- Header -->
            <div class="px-5 py-4 border-b border-white/10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">💬</div>
                <div>
                    <h1 class="font-semibold">Teman Curhat</h1>
                    <p class="text-xs text-white/60">AI yang mendengarkan</p>
                </div>
            </div>

            <!-- Messages -->
            <div id="chatBox" class="flex-1 overflow-y-auto px-4 py-4 space-y-3"></div>

            <!-- Input -->
            <div class="p-4 border-t border-white/10">
                <div class="flex gap-2">
                    <input id="messageInput" type="text" placeholder="Ceritakan isi hatimu…"
                        class="flex-1 bg-white/10 rounded-full px-4 py-2 outline-none placeholder:text-white/50" />
                    <button onclick="sendMessage()"
                        class="px-4 py-2 rounded-full bg-white/20 hover:bg-white/30 transition">➤</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const API_BASE = '{{ url('/api') }}';
    let curhatId = null;

    const chatBox = document.getElementById('chatBox');
    const input = document.getElementById('messageInput');
    const historyList = document.getElementById('historyList');

    /* ================= UI ================= */
    function addMessage(text, sender) {
        const wrap = document.createElement('div');
        wrap.className = sender === 'user' ? 'flex justify-end' : 'flex justify-start';
        const bubble = document.createElement('div');
        bubble.className = `px-4 py-2 rounded-2xl max-w-[80%] text-sm ${sender === 'user'
            ? 'bg-blue-500/40 rounded-br-none'
            : 'bg-white/20 rounded-bl-none'}`;
        bubble.innerText = text;
        wrap.appendChild(bubble);
        chatBox.appendChild(wrap);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    /* ================= CHAT ================= */
    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;
        addMessage(text, 'user');
        input.value = '';

        const loading = document.createElement('div');
        loading.className = 'text-xs text-white/50';
        loading.innerText = 'AI sedang mengetik...';
        chatBox.appendChild(loading);

        const res = await fetch(`${API_BASE}/curhat`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text, anonymous: true })
        });

        const data = await res.json();
        chatBox.removeChild(loading);

        curhatId = data.curhat.id;
        loadHistory();

        const ai = data.curhat.messages.find(m => m.sender === 'ai');
        if (ai) addMessage(ai.message, 'ai');
    }

    /* ================= HISTORY ================= */
    async function loadHistory() {
        const res = await fetch(`${API_BASE}/curhat`);
        const data = await res.json();

        historyList.innerHTML = '';
        data.data.forEach(item => {
            const btn = document.createElement('button');
            btn.className = 'w-full text-left p-3 rounded-xl bg-white/10 hover:bg-white/20 transition';
            btn.innerHTML = `<div class='text-sm font-medium truncate'>${item.title ?? 'Curhat Tanpa Judul'}</div>
                             <div class='text-xs text-white/50'>${new Date(item.created_at).toLocaleDateString()}</div>`;
            btn.onclick = () => openSession(item.id);
            historyList.appendChild(btn);
        });
    }

    async function openSession(id) {
        chatBox.innerHTML = '';
        const res = await fetch(`${API_BASE}/curhat/${id}`);
        const data = await res.json();
        curhatId = id;
        data.messages.forEach(m => addMessage(m.message, m.sender));
    }

    function newSession() {
        curhatId = null;
        chatBox.innerHTML = '';
    }

    loadHistory();
</script>

</body>
</html>