<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'admin.only' => \App\Http\Middleware\EnsureUserIsAdminOnly::class,
        ]);
        // Send unauthenticated users to admin login (route is named admin.login)
        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        // Exclude Facebook Webhook from CSRF protection
        $middleware->validateCsrfTokens(except: [
            'facebook/webhook',
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Fetch Facebook posts every hour from all configured pages
        $schedule->command('facebook:fetch-posts --use-db')
                 ->hourly()
                 ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if (
                $e->getStatusCode() === 403
                && $e->getMessage() === 'You do not have access to this department.'
                && ! $request->expectsJson()
                && $request->routeIs('admin.*')
                && $request->user()
            ) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('error', 'You do not have access to this department.');
            }

            return null;
        });
    })->create();
