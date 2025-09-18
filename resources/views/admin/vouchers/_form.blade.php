{{-- resources/views/admin/vouchers/_form.blade.php --}}
@csrf
<div class="row g-2">
  <div class="col-md-4">
    <label class="form-label">Mã (CODE)</label>
    <input name="code" class="form-control text-uppercase" value="{{ old('code', $voucher->code) }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Loại</label>
    <select name="type" class="form-select">
      <option value="percent" @selected(old('type',$voucher->type)=='percent')>% (phần trăm)</option>
      <option value="fixed"   @selected(old('type',$voucher->type)=='fixed')>Số tiền cố định</option>
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Giá trị</label>
    <input type="number" step="0.01" min="0" name="value" class="form-control" value="{{ old('value', $voucher->value) }}" required>
  </div>

  <div class="col-md-4">
    <label class="form-label">Trần giảm (optional)</label>
    <input type="number" step="0.01" min="0" name="max_discount" class="form-control" value="{{ old('max_discount', $voucher->max_discount) }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Đơn tối thiểu</label>
    <input type="number" step="0.01" min="0" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $voucher->min_order_amount) }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Giới hạn tổng lượt</label>
    <input type="number" min="1" name="total_limit" class="form-control" value="{{ old('total_limit', $voucher->total_limit) }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Giới hạn / user</label>
    <input type="number" min="1" name="per_user_limit" class="form-control" value="{{ old('per_user_limit', $voucher->per_user_limit) }}">
  </div>

  <div class="col-md-4">
    <label class="form-label">Bắt đầu</label>
    <input type="datetime-local" name="start_at" class="form-control"
           value="{{ old('start_at', optional($voucher->start_at)->format('Y-m-d\TH:i')) }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Kết thúc</label>
    <input type="datetime-local" name="end_at" class="form-control"
           value="{{ old('end_at', optional($voucher->end_at)->format('Y-m-d\TH:i')) }}">
  </div>

  <div class="col-12">
    <label class="form-label">Mô tả</label>
    <textarea name="description" rows="2" class="form-control">{{ old('description', $voucher->description) }}</textarea>
  </div>

  <div class="col-12 mt-2">
    <label class="form-check">
      <input type="checkbox" name="is_active" value="1" class="form-check-input"
             @checked(old('is_active', (int)$voucher->is_active))>
      <span class="form-check-label">Kích hoạt</span>
    </label>
  </div>
</div>
