<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'payment/webhooks/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->expectsJson() || ! in_array($request->method(), ['GET', 'HEAD'], true)) {
                return null;
            }

            $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : null;
            $shouldRedirect = $e instanceof InvalidSignatureException || in_array($statusCode, [403, 404], true);

            if (! $shouldRedirect || $request->routeIs('admin.dashboard', 'partner.dashboard', 'member.dashboard')) {
                return null;
            }

            $user = $request->user();

            if (! $user) {
                return redirect()->route('login')->with('error', 'Halaman tidak dapat diakses. Silakan login kembali.');
            }

            $dashboardRoute = match ($user->role) {
                'admin' => 'admin.dashboard',
                'vendor', 'mentor', 'partner' => 'partner.dashboard',
                default => 'member.dashboard',
            };

            return redirect()->route($dashboardRoute)->with('error', 'Halaman sudah expired atau tidak dapat diakses.');
        });
    })->create();
