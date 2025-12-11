<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curhat;
use App\Models\CurhatMessage;
use Illuminate\Support\Facades\Http;

class CurhatController extends Controller
{
    public function index()
    {
        // public listing (only meta, no full user identities)
        return Curhat::select('id', 'title', 'category', 'status', 'created_at')
            ->orderBy('id', 'desc')->paginate(20);
    }

    public function show($id)
    {
        $curhat = Curhat::with('messages')->findOrFail($id);
        return response()->json($curhat);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'title' => 'nullable|string|max:255',
            'anonymous' => 'nullable|boolean',
            'category' => 'nullable|string|max:100'
        ]);

        $curhat = Curhat::create([
            'title' => $request->title,
            'message' => $request->message,
            'anonymous' => $request->boolean('anonymous', true),
            'category' => $request->category
        ]);

        // simpan message awal sebagai sender=user
        CurhatMessage::create([
            'curhat_id' => $curhat->id,
            'sender' => 'user',
            'message' => $request->message
        ]);

        // kirim ke AI async-like (synchronous simple impl)
        $aiReply = $this->callGemini($request->message, $curhat->category);

        // simpan jawaban AI
        CurhatMessage::create([
            'curhat_id' => $curhat->id,
            'sender' => 'ai',
            'message' => $aiReply
        ]);

        // update status
        $curhat->update(['status' => 'answered']);

        return response()->json([
            'curhat' => $curhat->load('messages')
        ], 201);
    }

    // helper untuk panggil Gemini
    protected function callGemini(string $userMessage, $category = null)
    {
        $systemPrompt = "Kamu adalah AI teman curhat yang lembut, empatik, " .
            "dan tidak memberikan saran medis. Berikan tanggapan yang menenangkan, " .
            "ringkas, dan ajak user untuk bercerita lebih lanjut." .
            ($category ? " Category: $category." : "");

        $payload = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => $systemPrompt . "\n\nUser: " . $userMessage]
                    ]
                ]
            ]
        ];

        // ENDPOINT FIX
        $url = "https://generativelanguage.googleapis.com/v1/models/" .
            env('MODEL') .
            ":generateContent?key=" . env('GEMINI_API_KEY');

        try {
            $response = Http::post($url, $payload);
            $data = $response->json();

            $reply = $data['candidates'][0]['content']['parts'][0]['text']
                ?? "Maaf, aku belum bisa merespon.";

            // Safety Untuk Kata Berbahaya
            if ($this->detectSelfHarm($userMessage)) {
                $reply = "Maaf, aku merasa kamu sedang mengalami hal yang sangat berat. " .
                    "Jika kamu merasa berbahaya atau berpikir untuk menyakiti diri sendiri, " .
                    "tolong segera hubungi layanan darurat setempat atau orang terdekat.";
            }

            return $reply;
        } catch (\Exception $e) {
            return "Maaf, server AI mengalami gangguan. (" . $e->getMessage() . ")";
        }
    }



    protected function detectSelfHarm($text)
    {
        $lower = strtolower($text);
        $keywords = ['bunuh', 'bunuh diri', 'mau mati', 'tidak mau hidup', 'akhiri hidup', 'suicide', 'kill myself', 'end my life'];
        foreach ($keywords as $k)
            if (str_contains($lower, $k)) return true;
        return false;
    }
}
