@extends('layouts.app')
@section('title','Chat #'.$conversation->id)

@push('styles')
<style>
  /* Khung chat */
  .chat-wrap{ display:flex; flex-direction:column; height:60vh; min-height:420px; }
  .chat-header{
    display:flex; align-items:center; justify-content:space-between;
    border-bottom:1px solid var(--border); padding-bottom:.5rem; margin-bottom:.75rem;
  }
  .chat-room{
    flex:1; overflow:auto; padding:8px; background:#f9fafb;
    border:1px solid var(--border); border-radius:12px;
  }
  .msg-row{ display:flex; margin:6px 0; gap:8px; }
  .msg-row.user{ justify-content:flex-start; }
  .msg-row.admin{ justify-content:flex-end; }

  .avatar{
    width:32px; height:32px; border-radius:50%;
    display:grid; place-items:center;
    font-size:.8rem; font-weight:800; color:#111827;
    background:#e5e7eb; border:1px solid var(--border);
    flex:0 0 32px;
  }
  .bubble{
    max-width:68%; padding:.5rem .65rem; border-radius:12px;
    box-shadow: var(--shadow-1); position:relative;
    word-break:break-word; white-space:pre-wrap;
  }

  /* user (khách) bên trái xanh nhạt, admin bên phải xanh dương */
  .msg-row.user .bubble{
    background:#e5f9ef; border:1px solid #bbf7d0; color:#065f46;
  }
  .msg-row.admin .bubble{
    background:#dbeafe; border:1px solid #bfdbfe; color:#1e3a8a;
  }

  .meta{
    margin-top:2px; font-size:.75rem; color:#6b7280; display:flex; align-items:center; gap:.4rem;
  }

  /* footer nhập tin nhắn cố định dưới */
  .chat-footer{
    display:flex; gap:.5rem; margin-top:.75rem;
  }
  .chat-footer .form-control{
    border-radius:10px;
  }

  /* tag trạng thái */
  .badge-open{ background:#dcfce7; color:#064e3b; border:1px solid #bbf7d0; }
  .badge-closed{ background:#fee2e2; color:#7f1d1d; border:1px solid #fecaca; }
</style>
@endpush

@section('content')
@php
  $u = $conversation->user;
  $isOpen = ($conversation->status ?? 'open') !== 'closed';
@endphp

<div class="chat-wrap">
  {{-- Header --}}
  <div class="chat-header">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar">{{ strtoupper(mb_substr($u->name ?? 'U'.$conversation->user_id,0,1)) }}</div>
      <div>
        <div class="fw-bold mb-0">
          {{ $u->name ?? ('User '.$conversation->user_id) }}
          <span class="text-muted">• #{{ $conversation->id }}</span>
        </div>
        <small class="text-muted">Bắt đầu: {{ $conversation->created_at->format('d/m/Y H:i') }}</small>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <span class="badge rounded-pill {{ $isOpen ? 'badge-open' : 'badge-closed' }}">
        {{ $isOpen ? 'Đang mở' : 'Đã đóng' }}
      </span>
      @if($isOpen)
      <a class="btn btn-outline-danger btn-sm"
         href="#"
         onclick="event.preventDefault(); document.getElementById('closeForm').submit();">
        Đóng hội thoại
      </a>
      @endif
    </div>
  </div>

  {{-- Khung tin nhắn --}}
  <div class="chat-room" id="adminChatBody">
    @foreach($conversation->messages as $m)
      @php
        $side = $m->sender_role === 'admin' ? 'admin' : 'user';
        $initial = $side === 'admin' ? 'A' : 'U';
      @endphp
      <div class="msg-row {{ $side }}">
        @if($side==='user')
          <div class="avatar">{{ $initial }}</div>
        @endif

        <div>
          <div class="bubble">
            {!! nl2br(e($m->body)) !!}
          </div>
          <div class="meta">
            <span>{{ $m->created_at->format('d/m H:i') }}</span>
            <span>•</span>
            <span class="text-uppercase">{{ $m->sender_role }}</span>
          </div>
        </div>

        @if($side==='admin')
          <div class="avatar">{{ $initial }}</div>
        @endif
      </div>
    @endforeach
  </div>

  {{-- Gửi tin nhắn --}}
  <form id="adminChatForm" method="POST" action="{{ route('admin.chats.send',$conversation) }}" class="chat-footer">
    @csrf
    <input type="hidden" name="body" id="hiddenBody">
    <textarea id="typingBox" class="form-control" rows="1" placeholder="Nhập trả lời..." {{ $isOpen ? '' : 'disabled' }}></textarea>
    <button class="btn btn-primary" {{ $isOpen ? '' : 'disabled' }}>Gửi</button>
  </form>

  {{-- Form đóng --}}
  <form id="closeForm" method="POST" action="{{ route('admin.chats.close',$conversation) }}" class="d-none">
    @csrf
  </form>
</div>

@push('scripts')
<script>
(function(){
  const bodyEl   = document.getElementById('adminChatBody');
  const form     = document.getElementById('adminChatForm');
  const typing   = document.getElementById('typingBox');
  const hidden   = document.getElementById('hiddenBody');
  let   lastId   = {{ $conversation->messages->last()->id ?? 0 }};
  let   polling  = true;

  // Auto scroll xuống cuối khi vào trang
  function scrollBottom(){ bodyEl.scrollTop = bodyEl.scrollHeight; }
  scrollBottom();

  // Gửi bằng Enter (Shift+Enter để xuống dòng)
  typing?.addEventListener('keydown', function(e){
    if(e.key === 'Enter' && !e.shiftKey){
      e.preventDefault();
      submitMsg();
    }
  });

  form?.addEventListener('submit', function(e){
    e.preventDefault();
    submitMsg();
  });

  function submitMsg(){
    const text = (typing.value || '').trim();
    if(!text) return;
    hidden.value = text;
    form.submit();
  }

  // Render message bubble
  function renderMsg(m){
    const side = m.sender_role === 'admin' ? 'admin' : 'user';
    const row  = document.createElement('div');
    row.className = 'msg-row ' + side;

    const avatar = document.createElement('div');
    avatar.className = 'avatar';
    avatar.textContent = side === 'admin' ? 'A' : 'U';

    const bubbleWrap = document.createElement('div');
    const bubble = document.createElement('div');
    bubble.className = 'bubble';
    bubble.innerHTML = (m.body || '').replace(/\n/g,'<br>');

    const meta = document.createElement('div');
    meta.className = 'meta';
    const dt = new Date(m.created_at);
    const ts = dt.toLocaleDateString('vi-VN', { day:'2-digit', month:'2-digit' }) + ' ' +
               dt.toLocaleTimeString('vi-VN', { hour:'2-digit', minute:'2-digit' });
    meta.innerHTML = `<span>${ts}</span><span>•</span><span class="text-uppercase">${m.sender_role}</span>`;

    bubbleWrap.appendChild(bubble);
    bubbleWrap.appendChild(meta);

    if(side==='user'){ row.appendChild(avatar); row.appendChild(bubbleWrap); }
    else{ row.appendChild(bubbleWrap); row.appendChild(avatar); }

    bodyEl.appendChild(row);
  }

  // Poll 3s/lần
  async function fetchNew(){
    if(!polling) return;
    try{
      const url = "{{ route('admin.chats.fetch',$conversation) }}" + (lastId ? `?after_id=${lastId}` : '');
      const res = await fetch(url, { headers:{ 'X-Requested-With':'XMLHttpRequest' }});
      if(!res.ok) return;
      const json = await res.json();
      if(json.messages && json.messages.length){
        json.messages.forEach(m=>{
          lastId = Math.max(lastId, m.id);
          renderMsg(m);
        });
        scrollBottom();
      }
    }catch(e){}
  }
  setInterval(fetchNew, 3000);
})();
</script>
@endpush
@endsection
