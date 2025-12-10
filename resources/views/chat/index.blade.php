<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Curhat — Glass UI</title>
    @vite(['resources/css/app.css'])
    <style>
        /* tambahan glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px) saturate(120%);
            -webkit-backdrop-filter: blur(10px) saturate(120%);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        body {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-3xl">
        <div class="glass rounded-3xl p-6 shadow-2xl">
            <header class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-2xl">💬</div>
                <div>
                    <h1 class="text-2xl font-semibold text-white">Curhat — Teman yang Mendengar</h1>
                    <p class="text-sm text-white/80">Aman, empatik, dan penuh perhatian.</p>
                </div>
            </header>

            <main class="bg-white/6 rounded-2xl p-4 max-h-[60vh] overflow-y-auto" id="chatBox">
                <!-- bubbles will be injected here -->
            </main>

            <form id="chatForm" class="mt-4 flex gap-3 items-end">
                <input id="title" placeholder="Judul (opsional)"
                    class="flex-1 p-3 rounded-xl bg-white/10 placeholder-white/70 text-white outline-none" />
                <input id="anonymous" type="checkbox" checked class="mr-2" />
                <label class="text-white/80 mr-3">Anonym</label>
                <button type="submit" class="px-5 py-3 rounded-xl bg-white/20 text-white">Kirim</button>
            </form>

            <div class="mt-3 flex items-center justify-between text-white/80 text-sm">
                <div>Tips: Ceritakan dengan nyaman — aku mendengar.</div>
                <div id="status">Offline</div>
            </div>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chatBox');
        const form = document.getElementById('chatForm');

        function addBubble(text, who = 'ai') {
            const wrapper = document.createElement('div');
            wrapper.className = who === 'ai' ? 'flex mb-3' : 'flex mb-3 justify-end';
            wrapper.innerHTML = `
    <div class="${who === 'ai' ? 'bg-white/12 text-white p-3 rounded-xl max-w-[70%]' : 'bg-white text-gray-900 p-3 rounded-xl max-w-[70%]'}">
      ${escapeHtml(text)}
    </div>
  `;
            chatBox.appendChild(wrapper);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function escapeHtml(unsafe) {
            return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = document.getElementById('title').value || null;
            const anonymous = document.getElementById('anonymous').checked;
            const message = prompt("Tulis curhatanmu (atau gunakan kolom judul untuk ringkasan):");

            if (!message) return;

            // show user bubble
            addBubble(message, 'user');

            // post to API
            document.getElementById('status').innerText = 'Mengirim...';

            const res = await fetch('/api/curhat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    title,
                    message,
                    anonymous
                })
            });
            const data = await res.json();
            document.getElementById('status').innerText = 'Online';

            // render messages returned
            if (data.curhat && data.curhat.messages) {
                chatBox.innerHTML = ''; // replace with server messages
                data.curhat.messages.forEach(m => addBubble(m.message, m.sender === 'ai' ? 'ai' : 'user'));
            }
        });
    </script>

</body>

</html>
