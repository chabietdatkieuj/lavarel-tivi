<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatAdminController extends Controller
{
    public function index()
    {
        // danh sách các cuộc chat đang mở, mới nhất
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
        $request->validate(['body'=>'required|string|max:2000']);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => auth()->id(),
            'sender_role'     => 'admin',
            'body'            => $request->body,
        ]);

        $conversation->update(['admin_id'=>auth()->id()]); // gán admin đang phụ trách

        return back();
    }

    public function fetch(Request $request, Conversation $conversation)
    {
        $request->validate(['after_id'=>'nullable|integer|min:0']);
        $q = Message::where('conversation_id', $conversation->id);
        if ($request->filled('after_id')) $q->where('id','>',(int)$request->after_id);
        return response()->json(['messages'=>$q->orderBy('id')->limit(200)->get()]);
    }

    public function close(Conversation $conversation)
    {
        $conversation->update(['status'=>'closed']);
        return back()->with('success','Đã đóng cuộc hội thoại.');
    }
}
