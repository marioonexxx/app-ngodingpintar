<?php

namespace App\Services;

use App\Models\ClassBatch;
use App\Models\Product;
use Carbon\CarbonImmutable;

class ClassBatchStatusService
{
    public function closeStartedBatches(?Product $product = null): int
    {
        $query = ClassBatch::query()
            ->open()
            ->whereNotNull('start_date');

        if ($product) {
            $query->where('product_id', $product->id);
        }

        $now = CarbonImmutable::now($this->timezone());
        $batchIds = $query->get()
            ->filter(fn (ClassBatch $batch) => $batch->hasStarted($now))
            ->pluck('id');

        if ($batchIds->isEmpty()) {
            return 0;
        }

        return ClassBatch::whereKey($batchIds)->update([
            'status' => ClassBatch::STATUS_CLOSED,
            'updated_at' => now(),
        ]);
    }

    public function timezone(): string
    {
        return config('app.class_timezone', 'Asia/Jakarta');
    }
}
