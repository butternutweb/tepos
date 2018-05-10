<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Butternut\SnapMidtrans;

/**
 * Implements Service Provider
 * bootstrapping the midtrans communication for this application
 * 
 * @package: butternut/snapmidtrans
 * @author: SuperKandjeng
 * 
 */
class SnapMidtransServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SnapMidtrans::class, function ($app) {
            return new SnapMidtrans(config('snapmidtrans.serverKey'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SnapMidtrans::class];
    }
}