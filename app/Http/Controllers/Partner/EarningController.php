<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\PartnerEarning;
use App\Support\Frontend;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Tab type: 'source_code' or 'class'
        $type = $request->query('type', 'source_code');

        $earnings = PartnerEarning::with(['product', 'transaction.user'])
            ->where('user_id', $user->id)
            ->whereHas('product', function ($query) use ($type) {
                $query->where('product_type', $type);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalEarnings = PartnerEarning::where('user_id', $user->id)
            ->whereHas('product', function ($query) use ($type) {
                $query->where('product_type', $type);
            })
            ->sum('net_earning');

        return Frontend::render('Partner/Earnings/Index', [
            'earnings' => $earnings,
            'totalEarnings' => $totalEarnings,
            'currentType' => $type,
            'isVendor' => in_array($user->role, ['vendor', 'partner']),
            'isMentor' => in_array($user->role, ['mentor', 'partner']),
        ]);
    }
}
