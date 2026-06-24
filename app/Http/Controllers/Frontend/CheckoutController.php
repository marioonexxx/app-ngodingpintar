<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CheckoutAddon;
use App\Models\CustomFeatureRequest;
use App\Models\PromoCode;
use App\Models\Transaction;
use App\Notifications\OrderPlacedNotification;
use App\Support\Frontend;
use App\Support\ProductPurchaseGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(private readonly ProductPurchaseGuard $purchaseGuard)
    {
    }

    public function show(Request $request)
    {
        $cart = $this->cart($request)->load(['items.product', 'items.classBatch']);
        $subtotal = (float) $cart->items->sum('subtotal');
        $promoInput = $request->string('promo_code')->trim()->toString();
        $promoCode = $this->resolvePromoCode($promoInput);
        $discountTotal = $this->discountTotal($subtotal, $promoCode);

        return Frontend::render('Frontend/Checkout', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'promoInput' => $promoInput,
            'promoCode' => $promoCode,
            'promoError' => $promoInput && ! $promoCode ? 'Kode promo tidak aktif atau tidak ditemukan.' : null,
            'discountTotal' => $discountTotal,
            'total' => max(0, $subtotal - $discountTotal),
            'gateways' => $this->availableGateways(max(0, $subtotal - $discountTotal)),
            'bankAccounts' => \App\Models\BankAccount::where('is_active', true)->get(),
            'checkoutAddons' => CheckoutAddon::active()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'payment_gateway' => ['required', 'in:free,manual_transfer,payment_gateway'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'addons' => ['nullable', 'array'],
            'addons.*' => ['integer', 'exists:checkout_addons,id'],
            'custom_feature_description' => ['nullable', 'string', 'max:2000'],
        ]);

        $cart = $this->cart($request)->load(['items.product', 'items.classBatch']);
        abort_if($cart->items->isEmpty(), 422, 'Cart masih kosong.');

        $conflict = $this->purchaseGuard->findConflict(
            $request->user(),
            $cart->items->pluck('product')
        );

        if ($conflict) {
            $cart->items()->where('product_id', $conflict['product']->id)->delete();

            if ($conflict['type'] === 'owned_class') {
                return redirect()->route('member.transactions.history')
                    ->with('error', "Kelas {$conflict['product']->name} sudah Anda miliki dan telah dihapus dari keranjang.");
            }

            return redirect()->route('member.transactions.active')
                ->with('error', "Produk {$conflict['product']->name} sudah ada pada invoice {$conflict['transaction']->invoice_number}. Silakan upload bukti transfer pada transaksi tersebut.");
        }

        $promoCode = $this->resolvePromoCode($data['promo_code'] ?? null);

        if (($data['promo_code'] ?? null) && ! $promoCode) {
            return back()->with('error', 'Kode promo tidak aktif atau tidak ditemukan.');
        }

        $transaction = DB::transaction(function () use ($cart, $request, $data, $promoCode) {
            $subtotal = (float) $cart->items->sum('subtotal');
            $discountTotal = $this->discountTotal($subtotal, $promoCode);

            // Calculate addon total (only paid addons)
            $addonTotal = 0;
            $selectedAddons = collect();

            if (! empty($data['addons'])) {
                $selectedAddons = CheckoutAddon::active()->whereIn('id', $data['addons'])->get();
                $addonTotal = (float) $selectedAddons->where('type', CheckoutAddon::TYPE_PAID)->sum('price');
            }

            $total = max(0, $subtotal - $discountTotal + $addonTotal);

            $isFree = $total <= 0;

            // Force free gateway when total is 0
            $gateway = $isFree ? 'free' : $data['payment_gateway'];

            // Reject payment_gateway (QR code) — not yet implemented
            if ($gateway === 'payment_gateway') {
                throw new \Exception('Pembayaran online belum tersedia.');
            }

            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'cart_id' => $cart->id,
                'promo_code_id' => $promoCode?->id,
                'invoice_number' => 'MDV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'payment_gateway' => $gateway,
                'promo_code' => $promoCode?->code,
                'discount_percent' => $promoCode?->discount_percent,
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'addon_total' => $addonTotal,
                'total' => $total,
                'status' => $isFree ? Transaction::STATUS_COMPLETED : Transaction::STATUS_PENDING,
                'payment_status' => $isFree ? Transaction::PAYMENT_PAID : Transaction::PAYMENT_UNPAID,
            ]);

            // Create transaction items
            foreach ($cart->items as $item) {
                $transaction->items()->create([
                    'product_id' => $item->product_id,
                    'class_batch_id' => $item->class_batch_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]);
            }

            // Create transaction addons
            foreach ($selectedAddons as $addon) {
                $transaction->addons()->create([
                    'checkout_addon_id' => $addon->id,
                    'name' => $addon->name,
                    'type' => $addon->type,
                    'price' => $addon->isPaid() ? $addon->price : null,
                    'notes' => $addon->isCustomRequest() ? ($data['custom_feature_description'] ?? null) : null,
                ]);

                // Create custom feature request if custom_request type and description provided
                if ($addon->isCustomRequest() && ! empty($data['custom_feature_description'])) {
                    // Get the first product from cart for reference
                    $firstProduct = $cart->items->first()?->product;

                    CustomFeatureRequest::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $firstProduct?->id,
                        'vendor_id' => null,
                        'user_id' => $request->user()->id,
                        'description' => $data['custom_feature_description'],
                        'status' => CustomFeatureRequest::STATUS_PENDING_REVIEW,
                    ]);
                }
            }

            $cart->update(['status' => Cart::STATUS_CHECKED_OUT]);
            $promoCode?->increment('used_count');

            $request->user()->notify(new OrderPlacedNotification($transaction));

            return $transaction;
        });

        if ($transaction->total <= 0) {
            return redirect()->route('member.transactions.history')
                ->with('success', "Checkout berhasil. Anda dapat langsung mengunduh produk Anda.");
        }

        return redirect()->route('member.transactions.active')
            ->with('success', "Checkout berhasil. Invoice {$transaction->invoice_number} menunggu pembayaran.");
    }

    private function cart(Request $request): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => $request->user()->id,
            'status' => Cart::STATUS_ACTIVE,
        ]);
    }

    private function resolvePromoCode(?string $code): ?PromoCode
    {
        if (! $code) {
            return null;
        }

        return PromoCode::query()
            ->where('code', Str::upper(Str::slug($code)))
            ->where('is_active', true)
            ->first();
    }

    private function discountTotal(float $subtotal, ?PromoCode $promoCode): float
    {
        if (! $promoCode) {
            return 0;
        }

        return round($subtotal * $promoCode->discount_percent / 100, 2);
    }

    private function availableGateways(float $total): array
    {
        if ($total <= 0) {
            return ['free'];
        }

        return ['manual_transfer', 'payment_gateway'];
    }
}
