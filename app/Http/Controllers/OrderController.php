<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /** Hiển thị form Checkout (lấy hàng từ carts/cart_items) */
    public function create(Request $request)
    {
        $cart  = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $items = $cart->items()->with('product')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        $total = $items->sum(fn($i) => $i->quantity * $i->price);

        return view('checkout.create', compact('items', 'total'));
    }

    /** Xử lý đặt hàng: COD mặc định; MoMo optional */
    public function store(Request $request)
    {
        $request->validate([
            'shipping_name'    => 'required|string|max:255',
            'shipping_phone'   => 'required|string|max:30',
            'shipping_address' => 'required|string|max:255',
            'payment_method'   => 'required|in:cod,momo',
            'note'             => 'nullable|string|max:2000',
        ]);

        $cart  = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $items = $cart->items()->with('product')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        DB::beginTransaction();
        try {
            // Check tồn kho
            foreach ($items as $ci) {
                if (!$ci->product || $ci->product->quantity < $ci->quantity) {
                    throw new \Exception("Sản phẩm {$ci->product->name} không đủ số lượng.");
                }
            }

            $total = $items->sum(fn($i) => $i->quantity * $i->price);

            // Tạo đơn
            $order = Order::create([
                'user_id'          => auth()->id(),
                'shipping_name'    => $request->shipping_name,
                'shipping_phone'   => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'payment_method'   => $request->payment_method,
                'status'           => $request->payment_method === 'cod' ? 'pending' : 'unpaid',
                'total_amount'     => $total,
                'note'             => $request->note,
            ]);

            // Lưu items + trừ tồn
            foreach ($items as $ci) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $ci->product_id,
                    'quantity'   => $ci->quantity,
                    'price'      => $ci->price,
                ]);
                $ci->product->decrement('quantity', $ci->quantity);
            }

            // Xóa giỏ
            $cart->items()->delete();
            DB::commit();

            // Nếu MoMo → tạo giao dịch (để sau)
            if ($request->payment_method === 'momo') {
                return $this->redirectToMoMo($order); // *Optional — có thể return redirect link*
            }

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Đặt hàng thành công! Thanh toán khi nhận hàng (COD).');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /** Lịch sử đơn */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
                        ->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    /** Chi tiết đơn (chỉ chủ đơn hoặc admin) */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id() && (auth()->user()->role ?? '') !== 'admin') {
            abort(403);
        }
        $order->load(['items.product']);
        return view('orders.show', compact('order'));
    }

    /* ==================== MoMo (Optional) ==================== */
    private string $momoEndpoint   = 'https://test-payment.momo.vn/v2/gateway/api/create';
    private string $momoPartner    = 'MOMOBKUN20180529';
    private string $momoAccessKey  = 'klm05TvNBzhg7h7j';
    private string $momoSecret     = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

    protected function redirectToMoMo(Order $order)
    {
        // Chuẩn bị payload
        $redirectUrl = route('payment.momo.callback');
        $ipnUrl      = route('payment.momo.ipn');
        $orderId     = time().'_'.$order->id;
        $requestId   = uniqid();
        $amount      = (string) max(1000, (int)$order->total_amount);
        $orderInfo   = "Thanh toán đơn #{$order->id}";
        $extraData   = '';
        $requestType = 'payWithATM';

        $rawHash = "accessKey={$this->momoAccessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$ipnUrl}".
                   "&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$this->momoPartner}".
                   "&redirectUrl={$redirectUrl}&requestId={$requestId}&requestType={$requestType}";
        $signature = hash_hmac('sha256', $rawHash, $this->momoSecret);

        $payload = [
            'partnerCode' => $this->momoPartner,
            'partnerName' => 'TV Store',
            'storeId'     => 'TV_Store',
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
        ];

        try {
            $res = Http::withHeaders(['Content-Type' => 'application/json; charset=UTF-8'])
                        ->withoutVerifying()
                        ->post($this->momoEndpoint, $payload);

            if (!$res->successful()) {
                return redirect()->route('orders.index')
                    ->with('error','Không kết nối được MoMo: '.$res->status());
            }
            $json = $res->json();
            if (!empty($json['payUrl'])) {
                return redirect()->away($json['payUrl']);
            }
            return redirect()->route('orders.index')
                ->with('error','MoMo không trả về payUrl.');
        } catch (\Throwable $e) {
            return redirect()->route('orders.index')
                ->with('error','Lỗi MoMo: '.$e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $result = (string)$request->input('resultCode'); // 0 success
        $order  = null;
        if ($request->filled('orderId')) {
            $parts  = explode('_', $request->orderId);
            $oid    = (int) end($parts);
            $order  = Order::find($oid);
        }
        if ($result === '0') {
            if ($order) $order->update(['status' => 'paid']);
            return redirect()->route('orders.index')->with('success','Thanh toán MoMo thành công!');
        } else {
            if ($order) $order->update(['status' => 'failed']);
            return redirect()->route('orders.index')->with('error','Thanh toán MoMo thất bại hoặc bị hủy.');
        }
    }

    public function ipn(Request $request)
    {
        // TODO: xác thực chữ ký
        return response()->json(['resultCode'=>0,'message'=>'OK']);
    }

    public function payAgain(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        if ($order->status === 'paid') {
            return redirect()->route('orders.index')->with('info','Đơn đã thanh toán.');
        }
        $order->update(['status'=>'unpaid']);
        return $this->redirectToMoMo($order);
    }
}
