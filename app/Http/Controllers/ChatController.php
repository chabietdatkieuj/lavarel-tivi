<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Lấy hoặc tạo cuộc trò chuyện của user hiện tại
    private function getOrCreateConversation($userId): Conversation
    {
        $conv = Conversation::where('user_id', $userId)->where('status','open')->first();
        if (!$conv) {
            $conv = Conversation::create(['user_id'=>$userId, 'status'=>'open']);
        }
        return $conv;
    }

    // API: lấy messages (polling)
    public function fetch(Request $request)
    {
        $request->validate([
            'after_id' => 'nullable|integer|min:0',
        ]);

        $conv = $this->getOrCreateConversation(auth()->id());
        $q = Message::where('conversation_id', $conv->id);
        if ($request->filled('after_id')) {
            $q->where('id', '>', (int)$request->after_id);
        }
        $messages = $q->orderBy('id')->limit(100)->get(['id','sender_role','body','created_at']);

        return response()->json([
            'conversation_id' => $conv->id,
            'messages' => $messages,
        ]);
    }

    // API: gửi message
    public function send(Request $request)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $conv = $this->getOrCreateConversation(auth()->id());

        $msg = Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => auth()->id(),
            'sender_role'     => 'user',
            'body'            => $request->body,
        ]);

        return response()->json(['ok'=>true, 'id'=>$msg->id]);
    }
}
