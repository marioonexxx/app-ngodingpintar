<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuplicateProductPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_with_active_invoice_cannot_be_added_again(): void
    {
        $user = User::factory()->create();
        $product = $this->product();
        $transaction = $this->transactionFor($user, $product);

        $response = $this->actingAs($user)->get(route('cart.buy-now', $product));

        $response->assertRedirect(route('member.transactions.active'));
        $response->assertSessionHas('error', fn ($message) => str_contains($message, $transaction->invoice_number));
        $this->assertDatabaseMissing('cart_items', ['product_id' => $product->id]);
    }

    public function test_paid_class_cannot_be_purchased_again(): void
    {
        $user = User::factory()->create();
        $product = $this->product('class');
        $this->transactionFor(
            $user,
            $product,
            Transaction::STATUS_COMPLETED,
            Transaction::PAYMENT_PAID
        );

        $response = $this->actingAs($user)->get(route('cart.buy-now', $product));

        $response->assertRedirect(route('member.transactions.history'));
        $response->assertSessionHas('error', fn ($message) => str_contains($message, 'sudah Anda miliki'));
        $this->assertDatabaseMissing('cart_items', ['product_id' => $product->id]);
    }

    public function test_checkout_does_not_create_duplicate_transaction_for_active_invoice(): void
    {
        $user = User::factory()->create();
        $product = $this->product();
        $this->transactionFor($user, $product);
        $cart = Cart::create(['user_id' => $user->id, 'status' => Cart::STATUS_ACTIVE]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->price,
            'subtotal' => $product->price,
        ]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'payment_gateway' => 'manual_transfer',
        ]);

        $response->assertRedirect(route('member.transactions.active'));
        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_same_product_only_appears_once_in_cart(): void
    {
        $user = User::factory()->create();
        $product = $this->product();

        $this->actingAs($user)->post(route('cart.store', $product));
        $response = $this->actingAs($user)->post(route('cart.store', $product));

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $this->assertDatabaseCount('cart_items', 1);
    }

    private function product(string $type = 'digital_product'): Product
    {
        $category = ProductCategory::create([
            'name' => 'Kategori '.uniqid(),
            'slug' => 'kategori-'.uniqid(),
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Produk '.uniqid(),
            'slug' => 'produk-'.uniqid(),
            'price' => 175000,
            'status' => Product::STATUS_ACTIVE,
            'product_type' => $type,
        ]);
        $product->categories()->attach($category);

        return $product;
    }

    private function transactionFor(
        User $user,
        Product $product,
        string $status = Transaction::STATUS_PENDING,
        string $paymentStatus = Transaction::PAYMENT_UNPAID,
    ): Transaction {
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'invoice_number' => 'TEST-'.uniqid(),
            'payment_gateway' => 'manual_transfer',
            'subtotal' => $product->price,
            'total' => $product->price,
            'status' => $status,
            'payment_status' => $paymentStatus,
        ]);
        $transaction->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => $product->price,
            'subtotal' => $product->price,
        ]);

        return $transaction;
    }
}
