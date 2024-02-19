<?php

namespace Cloud\TceCloud;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Cloud\TceCloud\Contracts\Factory;

class TceCloudServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('tcecloud.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton(Factory::class, function ($app) {
            return new ProductManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Factory::class];
    }
}
