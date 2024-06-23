<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

class ApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $response = app(ResponseFactory::class);

        $response->macro('success', function (bool $status, string $message, array $data = null) use ($response) {
            return $response->json([
                'status' => $status,
                'message' => $message,
                'data' => $data
            ], 200); // Use a standard HTTP status code for success, such as 200
        });

        $response->macro('error', function (bool $status, string $message, $errors = null) use ($response) {
            $errors = is_string($errors) ? [$errors] : (array) $errors;

            return $response->json([
                'status' => $status,
                'message' => $message,
                'errors' => $errors
            ], 400); // Use a standard HTTP status code for errors, such as 400
        });
    }
}
