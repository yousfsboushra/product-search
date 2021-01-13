<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Feed\Ebay;
use App\Services\Feed\Amazon;

class FeedServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){
        $this->app->singleton(Ebay::class, function ($app) {
            return new Ebay(env('EBAY_ENDPOINT'), env('EBAY_APP_ID'), env('EBAY_CACHE_TIME'));
        });

        $this->app->singleton(Amazon::class, function ($app) {
            return new Amazon();
        });

        $this->app->tag([Ebay::class], 'feeds');
    }
}
