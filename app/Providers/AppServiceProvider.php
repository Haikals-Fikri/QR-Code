<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

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
        // Arahkan user yang sudah login ke dashboard sesuai role.
        RedirectIfAuthenticated::redirectUsing(function (Request $request): string {
            $user = Auth::user();

            if ($user?->isAdmin()) {
                return route('admin.dashboard', absolute: false);
            }

            return route('dashboard', absolute: false);
        });
    }
}
