<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JsonResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->resolving(\Illuminate\Http\Request::class, function ($request, $app) {
            $request->headers->set('Accept', 'application/json');
        });
    }
}
