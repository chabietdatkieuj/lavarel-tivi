<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    private function getOrCreateConversation($userId)
    {
        $conv = \App\Models\Conversation::where('user_id', $userId)->where('status','open')->first();
        if (!$conv) $conv = \App\Models\Conversation::create(['user_id'=>$userId, 'status'=>'open']);
        return $conv;
    }

    // Lấy messages (kèm ảnh)
    public function fetch(Request $request)
    {
        $request->validate(['after_id' => 'nullable|integer|min:0']);

        $conv = $this->getOrCreateConversation(auth()->id());

        $q = Message::where('conversation_id', $conv->id);
        if ($request->filled('after_id')) {
            $q->where('id', '>', (int)$request->after_id);
        }

        // Lấy cả image_path để frontend render ảnh
        $messages = $q->orderBy('id')
                      ->limit(100)
                      ->get(['id','sender_role','body','image_path','created_at']);

        // Model có accessor image_url => tự append trong JSON nếu đã set $appends
        return response()->json([
            'conversation_id' => $conv->id,
            'messages'        => $messages,
        ]);
    }

    // Gửi message (text/ảnh hoặc cả hai)
    public function send(Request $request)
    {
        $request->validate([
            'body'  => 'nullable|string|max:2000',
            'image' => 'nullable|image|max:3072', // <= 3MB
        ]);

        $conv  = $this->getOrCreateConversation(auth()->id());
        $body  = trim((string) $request->input('body', ''));
        $path  = null;

        if ($request->hasFile('image')) {
            // lưu vào storage/app/public/chat
            $path = $request->file('image')->store('chat', 'public');
        }

        // nếu không có text và cũng không có ảnh thì từ chối
        if ($body === '' && !$path) {
            return response()->json(['ok'=>false, 'error'=>'EMPTY_MESSAGE'], 422);
        }

        $msg = Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => auth()->id(),
            'sender_role'     => 'user',
            'body'            => $body,       // chuỗi rỗng thay vì null
            'image_path'      => $path,       // có thể null
        ]);

        // Trả full message (có image_url do accessor trong Model)
        return response()->json(['ok'=>true, 'message'=>$msg]);
    }
}
