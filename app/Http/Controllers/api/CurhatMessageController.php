<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curhat;
use App\Models\CurhatMessage;

class CurhatMessageController extends Controller
{
    public function send(Request $request, $id)
    {
        $request->validate(['message'=>'required|string']);

        $curhat = Curhat::findOrFail($id);

        // simpan pesan user
        $msg = CurhatMessage::create([
            'curhat_id' => $curhat->id,
            'sender' => 'user',
            'message' => $request->message
        ]);

        // panggil AI dengan konteks (ambil beberapa message terakhir)
        $last = $curhat->messages()->orderBy('created_at','desc')->take(6)->get()->reverse();
        $context = $last->pluck('message')->join("\n");

        // panggil Gemini via CurhatController helper (reuse)
        $curhatCtrl = new CurhatController();
        $aiReply = $curhatCtrl->callGemini($context . "\nUser: " . $request->message, $curhat->category);

        // simpan jawaban AI
        $ai = CurhatMessage::create([
            'curhat_id' => $curhat->id,
            'sender' => 'ai',
            'message' => $aiReply
        ]);

        $curhat->update(['status' => 'answered']);

        return response()->json($curhat->load('messages'));
    }
}