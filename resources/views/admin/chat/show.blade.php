{{-- resources/views/admin/chats/show.blade.php --}}
@extends('layouts.app')
@section('title','Chat #'.$conversation->id)

@push('styles')
<style>
  .chat-page{
    display:flex; flex-direction:column;
    height: calc(100vh - 140px); /* luôn vừa màn hinh */
  }
  .chat-header{
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 0; margin-bottom:6px; border-bottom:1px solid var(--border);
  }

  /* Khung hội thoại có thanh cuộn RIÊNG */
  .chat-room{
    flex:1;
    height: 64vh;            /* cố định tỉ lệ, nhìn gọn */
    max-height: 64vh;
    overflow: auto;
    padding: 12px;
    background:#f8fafc;
    border:1px solid var(--border);
    border-radius:14px;
  }

  /* Composer (gửi tin) cố định dưới, gọn */
  .composer{
    display:flex; gap:.5rem; margin-top:10px;
  }
  .composer input[type="file"]{ display:none; }
  .attach-btn{
    width:36px; height:36px; border:1px solid var(--border); border-radius:10px;
    background:#fff; display:inline-flex; align-items:center; justify-content:center;
  }

  /* Bubble gọn hơn */
  .msg{ display:flex; gap:8px; margin:6px 0 }
  .msg.admin{ justify-content:flex-end }
  .avatar{
    width:28px; height:28px; border-radius:50%;
    display:grid; place-items:center; font-weight:800;
    background:#e5e7eb; color:#111; border:1px solid var(--border); font-size:.8rem;
  }
  .bubble{
    max-width: 55%;           /* từ 70% -> 55% cho gọn */
    border-radius:14px; padding:.5rem .6rem;
    box-shadow:var(--shadow-1); word-break:break-word; white-space:pre-wrap;
  }
  .msg.user  .bubble{ background:#e8f7ef; border:1px solid #bbf7d0; color:#065f46 }
  .msg.admin .bubble{ background:#e3efff; border:1px solid #bfdbfe; color:#1e3a8a }
  .meta{ margin-top:3px; font-size:.75rem; color:#6b7280 }

  /* Ảnh trong bubble thu nhỏ hợp lý */
  .bubble img.msg-img{
    display:block; max-width:280px; max-height:280px; width:auto; height:auto;
    border-radius:10px; border:1px solid rgba(0,0,0,.06); cursor:zoom-in;
  }

  .status-badge{ border-radius:999px; padding:.2rem .55rem; font-weight:700 }
  .is-open{ background:#dcfce7; color:#064e3b; border:1px solid #bbf7d0 }
  .is-closed{ background:#fee2e2; color:#7f1d1d; border:1px solid #fecaca }

  .to-bottom{
    position:absolute; right:16px; bottom:16px; z-index:3;
    background:#111827; color:#fff; border:none; border-radius:999px;
    padding:.45rem .6rem; box-shadow:0 8px 18px rgba(0,0,0,.25); display:none;
  }
</style>
@endpush

@section('content')
@php
  $u      = $conversation->user;
  $isOpen = ($conversation->status ?? 'open') !== 'closed' ? true : false;
@endphp

<div class="chat-page">
  {{-- HEADER --}}
<div class="chat-header">
  <div class="d-flex align-items-center gap-2">
    {{-- NÚT QUAY LẠI --}}
    <a href="{{ route('admin.chats.index') }}" class="btn btn-outline-secondary btn-sm">
      ← Quay lại
    </a>

    <div class="avatar">{{ strtoupper(mb_substr($u->name ?? 'U',0,1)) }}</div>
    <div>
      <div class="fw-bold">
        {{ $u->name ?? ('User '.$conversation->user_id) }}
        <span class="text-muted">• #{{ $conversation->id }}</span>
      </div>
      <small class="text-muted">Bắt đầu: {{ $conversation->created_at->format('d/m/Y H:i') }}</small>
    </div>
  </div>

  <div class="d-flex align-items-center gap-2">
    <span class="status-badge {{ $isOpen ? 'is-open':'is-closed' }}">
      {{ $isOpen ? 'Đang mở' : 'Đã đóng' }}
    </span>
    @if($isOpen)
      <a class="btn btn-outline-danger btn-sm"
         href="#"
         onclick="event.preventDefault();document.getElementById('closeForm').submit();">
        Đóng hội thoại
      </a>
    @endif
  </div>
</div>


  {{-- ROOM --}}
  <div class="position-relative">
    <div class="chat-room" id="room">
      @foreach($conversation->messages as $m)
        @php
          $side   = $m->sender_role === 'admin' ? 'admin' : 'user';
          $init   = $side === 'admin' ? 'A' : 'U';
          $hasTxt = filled($m->body);
          $hasImg = filled($m->image_path ?? null);
          $imgUrl = method_exists($m, 'getImageUrlAttribute') ? $m->image_url : ($hasImg ? asset('storage/'.$m->image_path) : null);
        @endphp
        <div class="msg {{ $side }}">
          @if($side==='user')<div class="avatar">{{ $init }}</div>@endif
          <div>
            <div class="bubble">
              @if($hasTxt)<div class="text">{!! nl2br(e($m->body)) !!}</div>@endif
              @if($imgUrl)
                <a href="{{ $imgUrl }}" target="_blank" rel="noopener">
                  <img class="msg-img" src="{{ $imgUrl }}" alt="image">
                </a>
              @endif
            </div>
            <div class="meta">{{ $m->created_at->format('d/m H:i') }} • {{ strtoupper($m->sender_role) }}</div>
          </div>
          @if($side==='admin')<div class="avatar">{{ $init }}</div>@endif
        </div>
      @endforeach
    </div>

    <button id="toBottom" class="to-bottom" type="button">⬇</button>
  </div>

  {{-- COMPOSER --}}
<form id="sendForm" method="POST" action="{{ route('admin.chats.send',$conversation) }}"
      class="composer" enctype="multipart/form-data">
  @csrf
  <label class="attach-btn" for="adminChatImage" title="Đính kèm ảnh">📎</label>
  <input type="file" id="adminChatImage" name="image" accept="image/*">

  <input type="hidden" name="body" id="hiddenBody">
  <textarea id="inputBox" class="form-control" rows="1" placeholder="Nhập trả lời..." {{ $isOpen ? '' : 'disabled' }}></textarea>
  <button class="btn btn-primary" {{ $isOpen ? '' : 'disabled' }}>Gửi</button>
</form>


  {{-- CLOSE FORM --}}
  <form id="closeForm" method="POST" action="{{ route('admin.chats.close',$conversation) }}" class="d-none">@csrf</form>
</div>

@push('scripts')
<script>
(function(){
  const room     = document.getElementById('room');
  const form     = document.getElementById('sendForm');
  const input    = document.getElementById('inputBox');
  const hidden   = document.getElementById('hiddenBody');
  const toBottom = document.getElementById('toBottom');

  let lastId = {{ $conversation->messages->last()->id ?? 0 }};

  // helpers
  const scrollBottom = () => { room.scrollTop = room.scrollHeight; }
  const nearBottom   = () => (room.scrollHeight - room.scrollTop - room.clientHeight) < 160;

  // auto resize textarea
  function autoSize(){
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 140) + 'px';
  }
  input?.addEventListener('input', autoSize);
  autoSize();

  // enter send / shift+enter newline
  input?.addEventListener('keydown', (e)=>{
    if(e.key==='Enter' && !e.shiftKey){
      e.preventDefault(); submitMsg();
    }
  });
  form?.addEventListener('submit', e => { e.preventDefault(); submitMsg(); });

  function submitMsg(){
    const text = (input.value || '').trim();
    if(!text) return;
    hidden.value = text;
    form.submit();
  }
  // auto submit khi chọn ảnh (gửi kèm text đang soạn nếu có)
const imgInput = document.getElementById('adminChatImage');
imgInput?.addEventListener('change', () => {
  if (imgInput.files && imgInput.files[0]) {
    hidden.value = (input.value || '').trim();
    form.submit();
  }
});


  // show jump-to-bottom when user scrolls up
  room.addEventListener('scroll', () => {
    toBottom.style.display = nearBottom() ? 'none' : 'block';
  });
  toBottom.addEventListener('click', scrollBottom);

  // render a message (support image)
  function render(m){
    const side = m.sender_role === 'admin' ? 'admin' : 'user';
    const wrap = document.createElement('div'); wrap.className = 'msg '+side;

    const avatar = document.createElement('div'); avatar.className='avatar'; avatar.textContent = side==='admin'?'A':'U';
    const bubbleWrap = document.createElement('div');
    const bubble = document.createElement('div'); bubble.className='bubble';

    if(m.body){ const t = document.createElement('div'); t.className='text'; t.innerHTML = (m.body||'').replace(/\n/g,'<br>'); bubble.appendChild(t); }
    if(m.image_url){
      const a = document.createElement('a'); a.href = m.image_url; a.target='_blank'; a.rel='noopener';
      const img = document.createElement('img'); img.className='msg-img'; img.src=m.image_url; img.alt='image';
      a.appendChild(img); bubble.appendChild(a);
    }

    const meta = document.createElement('div'); meta.className='meta';
    const dt   = new Date(m.created_at);
    meta.textContent = dt.toLocaleDateString('vi-VN',{day:'2-digit',month:'2-digit'})+' '+dt.toLocaleTimeString('vi-VN',{hour:'2-digit',minute:'2-digit'})+' • '+String(m.sender_role||'').toUpperCase();

    bubbleWrap.appendChild(bubble); bubbleWrap.appendChild(meta);

    if(side==='user'){ wrap.appendChild(avatar); wrap.appendChild(bubbleWrap); }
    else { wrap.appendChild(bubbleWrap); wrap.appendChild(avatar); }

    room.appendChild(wrap);
  }

  // polling
  async function fetchNew(){
    try{
      const url = "{{ route('admin.chats.fetch',$conversation) }}" + (lastId ? `?after_id=${lastId}` : '');
      const res = await fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}, cache:'no-store'});
      if(!res.ok) return;
      const data = await res.json();
      if(data.messages && data.messages.length){
        const stick = nearBottom();
        data.messages.forEach(m => { lastId = Math.max(lastId, m.id); render(m); });
        if(stick) scrollBottom();
      }
    }catch(e){}
  }
  setInterval(fetchNew, 3000);
  // first load stick to bottom
  scrollBottom();
})();
</script>
@endpush
@endsection
