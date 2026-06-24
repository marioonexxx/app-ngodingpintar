<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\TransactionItem;
use App\Support\Frontend;
use Illuminate\Http\Request;

class ClassParticipantController extends Controller
{
    public function index()
    {
        $classes = Product::where('product_type', 'class')->latest()->paginate(12);

        return Frontend::render('Admin/ClassParticipants/Index', [
            'classes' => $classes,
        ]);
    }

    public function show(Request $request, Product $product)
    {
        abort_unless($product->product_type === 'class', 404);

        $batches = $product->classBatches()->orderByDesc('batch_number')->get();
        $selectedBatchId = $request->integer('batch_id') ?: $batches->first()?->id;

        $participants = TransactionItem::with(['transaction.user'])
            ->where('product_id', $product->id)
            ->when($selectedBatchId, fn ($query) => $query->where('class_batch_id', $selectedBatchId))
            ->whereHas('transaction', function ($query) {
                $query->where('payment_status', \App\Models\Transaction::PAYMENT_PAID);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Frontend::render('Admin/ClassParticipants/Show', [
            'product' => $product,
            'batches' => $batches,
            'selectedBatchId' => $selectedBatchId,
            'participants' => $participants,
        ]);
    }
}
