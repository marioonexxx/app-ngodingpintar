<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Support\Frontend;

class OrderController extends Controller
{
    public function index()
    {
        return Frontend::render('Admin/Orders', [
            'orders' => Transaction::with(['user', 'items.product'])
                ->whereIn('status', [Transaction::STATUS_PENDING, Transaction::STATUS_PROCESSING])
                ->latest()
                ->paginate(15),
        ]);
    }
}
