<?php

namespace ApnaPayment\Settlements;

use Illuminate\Support\ServiceProvider;

class SettlementServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        // Publish configuration file to the app's config directory
        $this->publishes([
            __DIR__ . '/../../config/settlement-sdk.php' => config_path('settlement-sdk.php'),
        ], 'config');
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        // Merge the config file with the application's config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/settlement-sdk.php',
            'settlement-sdk'
        );
    }
}
