<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatAdminController extends Controller
{
    public function index()
    {
        $convs = Conversation::with('user')
            ->where('status','open')
            ->orderBy('updated_at','desc')
            ->paginate(15);

        return view('admin.chat.index', compact('convs'));
    }

    public function show(Conversation $conversation)
    {
        $conversation->load(['user','messages']);
        return view('admin.chat.show', compact('conversation'));
    }

    public function send(Request $request, Conversation $conversation)
    {
        // CHO PHÉP gửi text hoặc ảnh (hoặc cả hai)
        $data = $request->validate([
            'body'  => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            // Lưu vào storage/app/public/chat (nhớ storage:link)
            $path = $request->file('image')->store('chat', 'public');
        }

        // Nếu không có text lẫn ảnh -> bỏ qua để tránh lỗi 1048 (body not null)
        if (empty($data['body']) && !$path) {
            return back();
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'sender_role'     => 'admin',
            'body'            => $data['body'] ?? '',
            'image_path'      => $path,
        ]);

        // gán admin đang phụ trách
        $conversation->update(['admin_id'=>Auth::id()]);

        return back();
    }

    public function fetch(Request $request, Conversation $conversation)
    {
        $request->validate(['after_id'=>'nullable|integer|min:0']);

        $q = Message::where('conversation_id', $conversation->id);
        if ($request->filled('after_id')) {
            $q->where('id','>',(int)$request->after_id);
        }

        // Trả về image_url để frontend hiển thị ảnh
        $messages = $q->orderBy('id')->limit(200)
            ->get(['id','sender_role','body','image_path','created_at'])
            ->map(fn($m)=>[
                'id'          => $m->id,
                'sender_role' => $m->sender_role,
                'body'        => $m->body,
                'image_url'   => $m->image_url, // accessor từ model
                'created_at'  => $m->created_at,
            ]);

        return response()->json(['messages'=>$messages]);
    }

    public function close(Conversation $conversation)
    {
        $conversation->update(['status'=>'closed']);
        return back()->with('success','Đã đóng cuộc hội thoại.');
    }
}
