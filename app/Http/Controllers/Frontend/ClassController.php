<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\Frontend;
use Illuminate\Http\Request;
use App\Services\ClassBatchStatusService;

class ClassController extends Controller
{
    public function __construct(private readonly ClassBatchStatusService $batchStatusService)
    {
    }

    public function index(Request $request)
    {
        $this->batchStatusService->closeStartedBatches();

        $products = Product::query()
            ->withApprovedRating()
            ->with(['categories', 'classProduct'])
            ->where('status', Product::STATUS_ACTIVE)
            ->where('product_type', 'class')
            ->whereHas('classProduct')
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->whereHas('categories', fn ($q) => $q->where('slug', $request->category));
            })
            ->when($request->filled('search'), function ($query) use ($request): void {
                $query->where('name', 'like', '%'.$request->search.'%');
            })
            ->when($request->filled('schedule_type'), function ($query) use ($request): void {
                $query->whereHas('classProduct', function ($q) use ($request) {
                    $q->where('schedule_type', $request->schedule_type);
                });
            })
            ->when($request->filled('date'), function ($query) use ($request): void {
                try {
                    $date = \Carbon\Carbon::parse($request->date);
                    $dayOfWeek = $date->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
                    
                    $query->whereHas('classProduct', function ($q) use ($date, $dayOfWeek) {
                        $q->where(function ($sub) use ($date, $dayOfWeek) {
                            $sub->where('schedule_type', 'fixed')
                                ->whereDate('start_time', $date->toDateString());
                        })->orWhere(function ($sub) use ($dayOfWeek) {
                            $sub->where('schedule_type', 'recurring')
                                ->whereJsonContains('recurring_days', (string)$dayOfWeek);
                        });
                    });
                } catch (\Exception $e) {
                    // Ignore invalid date
                }
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Frontend::render('Frontend/ClassListing', [
            'products' => $products,
            'categories' => ProductCategory::query()
                ->where('is_active', true)
                ->where('product_type', 'class')
                ->withCount(['products' => fn ($query) => $query->where('status', Product::STATUS_ACTIVE)->where('product_type', 'class')])
                ->orderBy('name')
                ->get(),
            'filters' => $request->only(['category', 'search', 'schedule_type', 'date']),
        ]);
    }

    public function show(Product $product)
    {
        $this->batchStatusService->closeStartedBatches($product);

        abort_unless($product->status === Product::STATUS_ACTIVE, 404);

        $product->load(['categories', 'membership', 'classProduct'])
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
