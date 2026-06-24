<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClassApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['categories', 'vendor', 'mentor', 'coachingTopic'])
            ->where('product_type', 'class')
            ->where('status', Product::STATUS_PENDING)
            ->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return Frontend::render('Admin/ClassApprovals', [
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
