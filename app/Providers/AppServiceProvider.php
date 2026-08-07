<?php

namespace App\Providers;

use App\Models\WaterSource;
use App\Observers\WaterSourceObserver;
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
}