<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Response::macro('success', function (bool $status, string $message, $data = null) {
            return Response::json([
                'status' => $status,
                'message' => $message,
                'data' => $data
            ], 200); // Use a standard HTTP status code for success, such as 200
        });

        Response::macro('error', function (bool $status, string $message, $errors = null) {
            return Response::json([
                'status' => $status,
                'message' => $message,
                'errors' => $errors
            ], 400); // Use a standard HTTP status code for success, such as 200
        });
    }
}
