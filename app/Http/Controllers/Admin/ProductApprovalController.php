<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['categories', 'vendor', 'mentor', 'coachingTopic'])
            ->where(function ($q) {
                $q->whereNotNull('vendor_id')
                  ->orWhereNotNull('mentor_id');
            })
            ->where(function ($q) {
                $q->where('product_type', '!=', 'class')->orWhereNull('product_type');
            })
            ->where('status', Product::STATUS_PENDING)
            ->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return Frontend::render('Admin/ProductApprovals', [
            'products' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function approve(Product $product): RedirectResponse
    {
        $product->update([
            'status' => Product::STATUS_ACTIVE,
            'rejection_reason' => null,
        ]);

        return back(303)->with('success', 'Produk berhasil disetujui dan diterbitkan.');
    }

    public function reject(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $product->update([
            'status' => Product::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back(303)->with('success', 'Produk ditolak.');
    }
}
