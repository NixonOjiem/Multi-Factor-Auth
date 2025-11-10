<?php

namespace App\Providers;

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
        // 3. MAKE 100% SURE THIS LINE IS HERE
        //Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
