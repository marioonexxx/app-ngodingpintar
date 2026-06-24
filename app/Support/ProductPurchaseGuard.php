<?php

namespace App\Support;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;

class ProductPurchaseGuard
{
    /**
     * @param  Collection<int, Product>  $products
     * @return array{type: 'active'|'owned_class', transaction: Transaction, product: Product}|null
     */
    public function findConflict(User $user, Collection $products): ?array
    {
        $products = $products->filter()->unique('id')->values();
        $productIds = $products->pluck('id')->all();

        if ($productIds === []) {
            return null;
        }

        $classProductIds = $products
            ->where('product_type', 'class')
            ->pluck('id')
            ->all();

        if ($classProductIds !== []) {
            $ownedClassTransaction = $user->transactions()
                ->with('items')
                ->where('payment_status', Transaction::PAYMENT_PAID)
                ->whereHas('items', fn ($query) => $query->whereIn('product_id', $classProductIds))
                ->latest()
                ->first();

            if ($ownedClassTransaction) {
                $productId = $ownedClassTransaction->items
                    ->first(fn ($item) => in_array($item->product_id, $classProductIds, true))
                    ?->product_id;

                return [
                    'type' => 'owned_class',
                    'transaction' => $ownedClassTransaction,
                    'product' => $products->firstWhere('id', $productId),
                ];
            }
        }

        $activeTransaction = $user->transactions()
            ->with('items')
            ->whereIn('status', [Transaction::STATUS_PENDING, Transaction::STATUS_PROCESSING])
            ->whereIn('payment_status', [Transaction::PAYMENT_UNPAID, Transaction::PAYMENT_VERIFYING])
            ->whereHas('items', fn ($query) => $query->whereIn('product_id', $productIds))
            ->latest()
            ->first();

        if (! $activeTransaction) {
            return null;
        }

        $productId = $activeTransaction->items
            ->first(fn ($item) => in_array($item->product_id, $productIds, true))
            ?->product_id;

        return [
            'type' => 'active',
            'transaction' => $activeTransaction,
            'product' => $products->firstWhere('id', $productId),
        ];
    }
}
