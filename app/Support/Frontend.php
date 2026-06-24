<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Frontend
{
    /**
     * Keeps routes renderable before inertia-laravel is installed.
     */
    public static function render(string $component, array $props = [])
    {
        $props = array_merge(static::shared(request()), $props);

        if (function_exists('inertia')) {
            return inertia($component, $props);
        }

        return view('app', [
            'page' => [
                'component' => $component,
                'props' => $props,
                'url' => request()->getRequestUri(),
                'version' => null,
            ],
        ]);
    }

    public static function shared(Request $request): array
    {
        $errors = $request->session()->get('errors');
        
        $cartCount = 0;
        $activeTxCount = 0;
        $historyTxCount = 0;
        if ($request->user()) {
            $cart = \App\Models\Cart::where('user_id', $request->user()->id)
                ->where('status', \App\Models\Cart::STATUS_ACTIVE)
                ->first();
            if ($cart) {
                $cartCount = $cart->items()->sum('quantity');
            }

            $activeTxCount = \App\Models\Transaction::where('user_id', $request->user()->id)
                ->whereIn('status', [\App\Models\Transaction::STATUS_PENDING, \App\Models\Transaction::STATUS_PROCESSING])
                ->count();

            $historyTxCount = \App\Models\Transaction::where('user_id', $request->user()->id)
                ->where(function ($query): void {
                    $query->whereIn('status', [\App\Models\Transaction::STATUS_COMPLETED, \App\Models\Transaction::STATUS_CANCELLED])
                        ->orWhere('payment_status', \App\Models\Transaction::PAYMENT_PAID);
                })->count();
        }

        return [
            'auth' => [
                'user' => $request->user()?->only(['id', 'name', 'email', 'role', 'avatar', 'email_verified_at']),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'errors' => $errors
                ? collect($errors->getBag('default')->getMessages())->map(fn ($messages) => $messages[0] ?? null)->toArray()
                : (object) [],
            'csrf' => csrf_token(),
            'routes' => [
                'home' => Route::has('home') ? route('home') : '/',
                'login' => Route::has('login') ? route('login') : '/login',
                'register' => Route::has('register') ? route('register') : '/register',
            ],
            'cart_count' => $cartCount,
            'active_transactions_count' => $activeTxCount,
            'history_transactions_count' => $historyTxCount,
            'active_promo_code' => \App\Models\PromoCode::where('is_active', true)->first(),
        ];
    }
}
