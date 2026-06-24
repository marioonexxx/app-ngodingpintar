<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class TransactionDummyTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $transactionsMapping = [
            1 => ['dummy-web-pos'],
            2 => ['dummy-web-kasir'],
            3 => ['dummy-app-android-ojek'],
            4 => ['dummy-siakad-web'],
            5 => ['dummy-iot-dashboard'],
            6 => ['dummy-kelas-react-js'],
            7 => ['dummy-kelas-flutter'],
            8 => ['dummy-kelas-data-analyst'],
            9 => ['dummy-kelas-figma-basic'],
            10 => ['dummy-kelas-ethical-hacking'],
            11 => ['dummy-code-template-1'],
            12 => ['dummy-class-master-2'],
            13 => ['dummy-code-template-3', 'dummy-class-master-3'],
            14 => ['dummy-code-template-4'],
            15 => ['dummy-class-master-5'],
        ];

        // Create a dummy image for payment proof
        $directory = public_path('img/payment-proofs');
        File::ensureDirectoryExists($directory);
        $dummyProofPath = 'img/payment-proofs/dummy-proof.png';
        if (!File::exists(public_path($dummyProofPath))) {
            // copy a default thumbnail as dummy proof
            File::copy(public_path('img/thumbnail/default-thumbnail.png'), public_path($dummyProofPath));
        }

        foreach ($transactionsMapping as $memberId => $productSlugs) {
            $user = User::where('email', "member{$memberId}+dummy@gmail.com")->first();
            if (!$user) continue;

            $subtotal = 0;
            $items = [];
            foreach ($productSlugs as $slug) {
                $product = Product::with('classBatches')->where('slug', $slug)->first();
                if (!$product) continue;

                $items[] = [
                    'product_id' => $product->id,
                    'class_batch_id' => $product->product_type === 'class' ? $product->classBatches->first()?->id : null,
                    'product_name' => $product->name,
                    'quantity' => 1,
                    'unit_price' => $product->price,
                    'subtotal' => $product->price,
                ];
                $subtotal += $product->price;
            }

            if (empty($items)) continue;

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'cart_id' => null,
                'invoice_number' => 'MDV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)) . $memberId,
                'payment_gateway' => 'manual_transfer',
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'addon_total' => 0,
                'total' => $subtotal,
                'status' => Transaction::STATUS_PENDING,
                'payment_status' => Transaction::PAYMENT_VERIFYING,
                'payment_proof' => $dummyProofPath, // Fake upload
            ]);

            foreach ($items as $itemData) {
                $transaction->items()->create($itemData);
            }
        }
    }
}
