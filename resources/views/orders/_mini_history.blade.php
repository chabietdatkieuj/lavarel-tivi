<div class="card bg-transparent border-0 mt-4">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="fw-bold text-white mb-0">🧾 Lịch sử mua hàng gần đây</h5>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-light btn-sm">
                Xem tất cả đơn
            </a>
        </div>

        @if(($orders ?? collect())->isEmpty())
            <div class="p-3 text-muted" style="background:rgba(255,255,255,.04);border-radius:12px;">
                Bạn chưa có đơn hàng nào.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Ngày</th>
                            <th>Tổng</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $o)
                            <tr>
                                <td>{{ $o->id }}</td>
                                <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold text-warning">{{ number_format($o->total_amount,0,',','.') }} đ</td>
                                <td class="text-uppercase">{{ $o->payment_method }}</td>
                                <td>
                                    <span class="badge
                                        @if($o->status === 'paid') bg-success
                                        @elseif($o->status === 'pending') bg-warning text-dark
                                        @elseif($o->status === 'failed') bg-danger
                                        @else bg-secondary @endif">
                                        {{ $o->status }}
                                    </span>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-sm" href="{{ route('orders.show', $o->id) }}">
                                        Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
