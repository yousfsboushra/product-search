<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DataArranger\PriceArranger;

class ArrangeServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){
        $this->app->singleton(PriceArranger::class, function ($app) {
            return new PriceArranger();
        });

        $this->app->tag([PriceArranger::class], 'price');
    }
}
