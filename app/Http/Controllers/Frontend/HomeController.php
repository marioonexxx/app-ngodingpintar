<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Testimonial;
use App\Support\Frontend;
use App\Services\ClassBatchStatusService;

class HomeController extends Controller
{
    public function __construct(private readonly ClassBatchStatusService $batchStatusService)
    {
    }

    public function __invoke()
    {
        $this->batchStatusService->closeStartedBatches();

        $latestClasses = Product::withApprovedRating()->with(['categories', 'classProduct'])
            ->where('status', Product::STATUS_ACTIVE)
            ->where('product_type', 'class')
            ->whereHas('classProduct')
            ->latest()
            ->limit(8)
            ->get();

        $latestProducts = Product::withApprovedRating()->with('categories')
            ->where('status', Product::STATUS_ACTIVE)
            ->where('product_type', 'source_code')
            ->latest()
            ->limit(8)
            ->get();


        $classCategories = ProductCategory::where('is_active', true)
            ->where('product_type', 'class')
            ->orderBy('name')
            ->get();

        $productCategories = ProductCategory::where('is_active', true)
            ->where('product_type', 'source_code')
            ->orderBy('name')
            ->get();

        return Frontend::render('Frontend/Landing', [
            'latestClasses' => $latestClasses,
            'latestProducts' => $latestProducts,
            'classCategories' => $classCategories,
            'productCategories' => $productCategories,
            'testimonials' => Testimonial::query()
                ->with(['user', 'product'])
                ->where('status', Testimonial::STATUS_APPROVED)
                ->latest('approved_at')
                ->limit(6)
                ->get(),
        ]);
    }
}
