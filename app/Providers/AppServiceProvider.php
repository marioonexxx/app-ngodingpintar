<?php

namespace App\Providers;

use App\Services\Payments\DokuPaymentService;
use App\Services\Payments\DuitkuPaymentService;
use App\Services\Payments\MidtransPaymentService;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, function () {
            return match (config('services.payment.default', env('PAYMENT_GATEWAY', 'doku'))) {
                'midtrans' => app(MidtransPaymentService::class),
                'duitku' => app(DuitkuPaymentService::class),
                default => app(DokuPaymentService::class),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Transaction::observe(\App\Observers\TransactionObserver::class);
    }
}
