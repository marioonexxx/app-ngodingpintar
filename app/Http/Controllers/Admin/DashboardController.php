<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\Transaction;
use App\Support\Frontend;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Frontend::render('Admin/Dashboard', [
            'metrics' => [
                'source_codes' => Product::where('product_type', 'source_code')->count(),
                'class_products' => Product::where('product_type', 'class')->count(),
                'pending_orders' => Transaction::where('status', Transaction::STATUS_PENDING)->count(),
                'paid_revenue' => Transaction::where('payment_status', Transaction::PAYMENT_PAID)->sum('total'),
                'pending_testimonials' => Testimonial::where('status', Testimonial::STATUS_PENDING)->count(),
            ],
            'latestTransactions' => Transaction::with('user')->latest()->limit(8)->get(),
        ]);
    }
}
