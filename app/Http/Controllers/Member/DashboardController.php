<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Support\Frontend;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        return Frontend::render('Member/Dashboard', [
            'activeTransactions' => $request->user()->transactions()
                ->with('items')
                ->whereIn('status', [Transaction::STATUS_PENDING, Transaction::STATUS_PROCESSING])
                ->latest()
                ->limit(5)
                ->get(),
            'paidCount' => $request->user()->transactions()
                ->where('payment_status', Transaction::PAYMENT_PAID)
                ->count(),
        ]);
    }
}
