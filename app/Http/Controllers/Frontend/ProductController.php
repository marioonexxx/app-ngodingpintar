<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\Frontend;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->withApprovedRating()
            ->with('categories')
            ->where('status', Product::STATUS_ACTIVE)
            ->where('product_type', 'source_code')
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->whereHas('categories', fn ($q) => $q->where('slug', $request->category));
            })
            ->when($request->filled('search'), function ($query) use ($request): void {
                $query->where('name', 'like', '%'.$request->search.'%');
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Frontend::render('Frontend/ProductListing', [
            'products' => $products,
            'categories' => ProductCategory::query()
                ->where('is_active', true)
                ->where('product_type', 'source_code')
                ->withCount(['products' => fn ($query) => $query->where('status', Product::STATUS_ACTIVE)->where('product_type', 'source_code')])
                ->orderBy('name')
                ->get(),
            'filters' => $request->only(['category', 'search']),
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->status === Product::STATUS_ACTIVE, 404);

        $product->load(['categories', 'membership', 'classProduct', 'testimonials' => function ($query) {
            $query->where('status', \App\Models\Testimonial::STATUS_APPROVED)
                  ->with('user:id,name,avatar')
                  ->latest();
        }])
            ->loadAvg([
                'testimonials as approved_testimonials_avg_rating' => fn ($query) => $query
                    ->where('status', \App\Models\Testimonial::STATUS_APPROVED),
            ], 'rating')
            ->loadCount([
                'testimonials as approved_testimonials_count' => fn ($query) => $query
                    ->where('status', \App\Models\Testimonial::STATUS_APPROVED),
            ]);

        return Frontend::render('Frontend/ProductDetail', [
            'product' => $product,
            'relatedProducts' => Product::query()
                ->withApprovedRating()
                ->with('categories')
                ->where('status', Product::STATUS_ACTIVE)
                ->whereHas('categories', function ($q) use ($product) {
                    $q->whereIn('product_categories.id', $product->categories->pluck('id'));
                })
                ->whereKeyNot($product->id)
                ->limit(4)
                ->get(),
        ]);
    }
}
