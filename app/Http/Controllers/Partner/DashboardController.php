<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Support\Frontend;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $totalEarnings = \App\Models\PartnerEarning::where('user_id', $user->id)->sum('net_earning');
        $recentEarnings = \App\Models\PartnerEarning::with('product')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return Frontend::render('Partner/Dashboard', [
            'user' => $user,
            'totalEarnings' => $totalEarnings,
            'recentEarnings' => $recentEarnings,
        ]);
    }
}
