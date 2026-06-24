<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Support\Frontend;

class FinanceReportController extends Controller
{
    public function index()
    {
        return Frontend::render('Admin/FinanceReport', [
            'summary' => [
                'gross_revenue' => Transaction::where('payment_status', Transaction::PAYMENT_PAID)->sum('total'),
                'pending_amount' => Transaction::where('payment_status', Transaction::PAYMENT_UNPAID)->sum('total'),
                'paid_transactions' => Transaction::where('payment_status', Transaction::PAYMENT_PAID)->count(),
            ],
            'monthly' => Transaction::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as total')
                ->where('payment_status', Transaction::PAYMENT_PAID)
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->orderByRaw('YEAR(created_at) desc, MONTH(created_at) desc')
                ->limit(12)
                ->get(),
        ]);
    }
}
