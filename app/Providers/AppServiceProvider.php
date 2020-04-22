<?php

namespace App\Providers;

use App\Models\Detail;
use App\Models\Order;
use App\Observers\DetailObserver;
use App\Observers\OrderObserver;
use App\Observers\UserObserver;
use App\User;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Order::observe(OrderObserver::class);
        Detail::observe(DetailObserver::class);
    }
}
