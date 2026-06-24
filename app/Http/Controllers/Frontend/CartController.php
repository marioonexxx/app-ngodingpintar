<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Support\Frontend;
use App\Support\ProductPurchaseGuard;
use App\Services\ClassBatchStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly ProductPurchaseGuard $purchaseGuard,
        private readonly ClassBatchStatusService $batchStatusService,
    ) {
    }

    public function show(Request $request)
    {
        return Frontend::render('Frontend/Cart', [
            'cart' => $this->cart($request)->load(['items.product', 'items.classBatch']),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $this->batchStatusService->closeStartedBatches($product);

        if ($redirect = $this->purchaseConflictRedirect($request, $product)) {
            return $redirect;
        }

        if ($redirect = $this->missingClassBatchRedirect($product)) {
            return $redirect;
        }

        $added = $this->addProduct($request, $product);

        if ($request->boolean('checkout')) {
            return redirect()->route('checkout.show')->with(
                $added ? 'success' : 'error',
                $added ? 'Produk ditambahkan. Lanjutkan checkout.' : 'Produk tersebut sudah ada di keranjang.'
            );
        }

        return redirect()->route('cart.show')->with(
            $added ? 'success' : 'error',
            $added ? 'Produk ditambahkan ke cart.' : 'Produk tersebut sudah ada di keranjang.'
        );
    }

    public function buyNow(Request $request, Product $product): RedirectResponse
    {
        $this->batchStatusService->closeStartedBatches($product);

        if ($redirect = $this->purchaseConflictRedirect($request, $product)) {
            return $redirect;
        }

        if ($redirect = $this->missingClassBatchRedirect($product)) {
            return $redirect;
        }

        $added = $this->addProduct($request, $product);

        return redirect()->route('checkout.show')->with(
            $added ? 'success' : 'error',
            $added ? 'Produk ditambahkan. Lanjutkan checkout.' : 'Produk tersebut sudah ada di keranjang.'
        );
    }

    private function addProduct(Request $request, Product $product): bool
    {
        abort_unless($product->status === Product::STATUS_ACTIVE, 404);

        $cart = $this->cart($request);
        $price = $product->sale_price ?? $product->price;
        $classBatchId = $product->product_type === 'class'
            ? $product->classProduct()->value('id')
            : null;

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        if ($item->exists) {
            return false;
        }

        $item->quantity = 1;
        $item->class_batch_id = $classBatchId;
        $item->unit_price = $price;
        $item->subtotal = $price;
        $item->save();

        return true;
    }

    private function missingClassBatchRedirect(Product $product): ?RedirectResponse
    {
        if ($product->product_type !== 'class' || $product->classProduct()->exists()) {
            return null;
        }

        return back()->with('error', 'Kelas belum memiliki batch yang dibuka untuk pendaftaran.');
    }

    private function purchaseConflictRedirect(Request $request, Product $product): ?RedirectResponse
    {
        $conflict = $this->purchaseGuard->findConflict($request->user(), collect([$product]));

        if (! $conflict) {
            return null;
        }

        if ($conflict['type'] === 'owned_class') {
            return redirect()->route('member.transactions.history')
                ->with('error', "Kelas {$product->name} sudah Anda miliki dan tidak dapat dibeli ulang.");
        }

        return redirect()->route('member.transactions.active')
            ->with('error', "Produk {$product->name} sudah ada pada invoice {$conflict['transaction']->invoice_number}. Silakan lanjutkan pembayaran pada transaksi tersebut.");
    }

    public function update(Request $request, int $item): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        $cart = $this->cart($request);
        $cartItem = $cart->items()->whereKey($item)->firstOrFail();
        $cartItem->update([
            'quantity' => $data['quantity'],
            'subtotal' => $data['quantity'] * $cartItem->unit_price,
        ]);

        return back()->with('success', 'Cart diperbarui.');
    }

    public function destroy(Request $request, int $item): RedirectResponse
    {
        $this->cart($request)->items()->whereKey($item)->delete();

        return back()->with('success', 'Item cart dihapus.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $this->cart($request)->items()->delete();

        return back()->with('success', 'Semua item di cart telah dihapus.');
    }

    private function cart(Request $request): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => $request->user()->id,
            'status' => Cart::STATUS_ACTIVE,
        ]);
    }
}
