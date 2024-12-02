<?php
namespace ApnaPayment\Settlements;

use Illuminate\Support\ServiceProvider;

class SettlementServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__ . '/../config/settlement-sdk.php' => config_path('settlement-sdk.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/settlement-sdk.php',
            'settlement-sdk'
        );
    }
}
