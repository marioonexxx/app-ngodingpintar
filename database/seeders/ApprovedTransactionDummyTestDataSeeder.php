<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApprovedTransactionDummyTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get dummy members
        $members = User::where('email', 'like', 'member%+dummy@gmail.com')->get();
        if ($members->isEmpty()) {
            $this->command->warn('No dummy members found. Please run DummyTestDataSeeder first.');
            return;
        }

        // Get dummy products (source code and classes)
        $sourceCodes = Product::where('product_type', 'source_code')->get();
        $classes = Product::where('product_type', 'class')->get();

        if ($sourceCodes->isEmpty() || $classes->isEmpty()) {
            $this->command->warn('No dummy products found. Please run DummyTestDataSeeder first.');
            return;
        }

        $dummyProofPath = 'img/payment-proofs/dummy-proof.png';

        foreach ($members as $member) {
            // Generate 2 approved source code transactions for each member
            for ($i = 0; $i < 2; $i++) {
                $product = $sourceCodes->random();
                $this->createApprovedTransaction($member, $product, $dummyProofPath);
            }

            // Generate 2 approved class transactions for each member
            for ($i = 0; $i < 2; $i++) {
                $product = $classes->random();
                $this->createApprovedTransaction($member, $product, $dummyProofPath);
            }
        }
        
        $this->command->info('Approved transactions seeded successfully.');
    }

    private function createApprovedTransaction(User $member, Product $product, string $proofPath)
    {
        $price = $product->sale_price ?: $product->price;
        $uniqueCode = rand(100, 999);
        $subtotal = $price + $uniqueCode;

        // 1. Create transaction as pending first
        $transaction = Transaction::create([
            'user_id' => $member->id,
            'cart_id' => null,
            'invoice_number' => 'MDV-APP-' . date('Ymd') . '-' . strtoupper(Str::random(8)),
            'payment_gateway' => 'manual_transfer',
            'subtotal' => $price,
            'discount_total' => 0,
            'addon_total' => 0,
            'total' => $price,
            'status' => Transaction::STATUS_PENDING,
            'payment_status' => Transaction::PAYMENT_VERIFYING,
            'payment_proof' => $proofPath,
        ]);

        // 2. Add transaction items
        $transaction->items()->create([
            'product_id' => $product->id,
            'class_batch_id' => $product->product_type === 'class' ? $product->classBatches->first()?->id : null,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => $price,
            'subtotal' => $price,
        ]);

        // 3. Update to PAId to trigger TransactionObserver
        $transaction->update([
            'payment_status' => Transaction::PAYMENT_PAID,
            'status' => Transaction::STATUS_COMPLETED,
            'paid_at' => now(),
        ]);
    }
}
