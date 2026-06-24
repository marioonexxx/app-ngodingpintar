<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\ClassBatch;
use App\Models\Product;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ClassBatchStatusService;

class ClassBatchController extends Controller
{
    public function __construct(private readonly ClassBatchStatusService $batchStatusService)
    {
    }

    public function index(Product $product)
    {
        $this->ensureClassProduct($product);
        $this->batchStatusService->closeStartedBatches($product);

        $batches = $product->classBatches()
            ->withCount([
                'transactionItems as paid_participants_count' => fn ($query) => $query
                    ->whereHas('transaction', fn ($transaction) => $transaction->where('payment_status', 'paid')),
            ])
            ->orderByDesc('batch_number')
            ->get();

        $batches->each(function (ClassBatch $batch) use ($product): void {
            $batch->setAttribute(
                'update_url',
                route('mentor.classes.batches.update', [
                    'product' => $product->id,
                    'batch' => $batch->id,
                ], false)
            );
            $batch->setAttribute(
                'delete_url',
                route('mentor.classes.batches.destroy', [
                    'product' => $product->id,
                    'batch' => $batch->id,
                ], false)
            );
        });

        return Frontend::render('Mentor/Classes/Batches', [
            'product' => $product,
            'batches' => $batches,
            'storeUrl' => route('mentor.classes.batches.store', ['product' => $product->id], false),
            'statuses' => [
                ClassBatch::STATUS_OPEN,
                ClassBatch::STATUS_CLOSED,
                ClassBatch::STATUS_COMPLETED,
                ClassBatch::STATUS_CANCELLED,
            ],
            'nextBatchNumber' => ((int) $product->classBatches()->max('batch_number')) + 1,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_if($product->product_type !== 'class', 404);
        abort_if($product->mentor_id !== $request->user()->id, 403);
        $data = $this->validated($request);

        $batch = DB::transaction(function () use ($product, $data): ClassBatch {
            Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $nextNumber = ((int) $product->classBatches()->max('batch_number')) + 1;

            if ($data['status'] === ClassBatch::STATUS_OPEN) {
                $product->classBatches()->open()->update(['status' => ClassBatch::STATUS_CLOSED]);
            }

            return $product->classBatches()->create([
                ...$data,
                'batch_number' => $nextNumber,
            ]);
        });

        $this->batchStatusService->closeStartedBatches($product);

        return back()->with('success', "Batch {$batch->batch_number} berhasil dibuat.");
    }

    public function update(Request $request, Product $product, ClassBatch $batch): RedirectResponse
    {
        abort_if($product->product_type !== 'class', 404);
        abort_if($batch->product_id !== $product->id, 404);
        abort_if($product->mentor_id !== $request->user()->id, 403);
        $data = $this->validated($request);

        DB::transaction(function () use ($product, $batch, $data): void {
            Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            if ($data['status'] === ClassBatch::STATUS_OPEN) {
                $product->classBatches()
                    ->open()
                    ->whereKeyNot($batch->id)
                    ->update(['status' => ClassBatch::STATUS_CLOSED]);
            }

            $batch->update($data);
        });

        $this->batchStatusService->closeStartedBatches($product);

        return back()->with('success', "Batch {$batch->batch_number} berhasil diperbarui.");
    }

    public function updateFromCollection(Request $request, Product $product): RedirectResponse
    {
        abort_if($product->product_type !== 'class', 404);
        abort_if($product->mentor_id !== $request->user()->id, 403);
        return $this->update($request, $product, $this->batchFromRequest($request, $product));
    }

    public function destroy(Request $request, Product $product, ClassBatch $batch): RedirectResponse
    {
        abort_if($product->product_type !== 'class', 404);
        abort_if($batch->product_id !== $product->id, 404);
        abort_if($product->mentor_id !== $request->user()->id, 403);

        if ($batch->transactionItems()->exists()) {
            return back()->with('error', 'Batch yang sudah memiliki transaksi tidak dapat dihapus. Ubah statusnya menjadi closed atau cancelled.');
        }

        $batch->delete();

        return back()->with('success', 'Batch berhasil dihapus.');
    }

    public function destroyFromCollection(Request $request, Product $product): RedirectResponse
    {
        abort_if($product->product_type !== 'class', 404);
        abort_if($product->mentor_id !== $request->user()->id, 403);
        return $this->destroy($request, $product, $this->batchFromRequest($request, $product));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'status' => ['required', 'in:open,closed,completed,cancelled'],
            'schedule_type' => ['required', 'in:fixed,recurring'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'recurring_days' => ['nullable', 'array'],
            'recurring_days.*' => ['integer', 'between:1,7'],
            'platform' => ['nullable', 'string', 'max:255'],
            'meeting_link' => ['nullable', 'url', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
        ]);
    }

    private function ensureClassProduct(Product $product): void
    {
        abort_unless($product->product_type === 'class', 404);
    }

    private function ensureBatchOwner(Product $product, ClassBatch $batch): void
    {
        $this->ensureClassProduct($product);
        abort_unless($batch->product_id === $product->id, 404);
    }

    private function batchFromRequest(Request $request, Product $product): ClassBatch
    {
        $data = $request->validate([
            'batch_id' => ['required', 'integer'],
        ]);

        return $product->classBatches()->whereKey($data['batch_id'])->firstOrFail();
    }
}
