<?php

namespace SofteriaTech\SmsPro;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use SofteriaTech\SmsPro\Channels\SmsProChannel;

class SmsProServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/smspro.php' => config_path('smspro.php'),
            ], 'config');
        }

        // Register the notification channel
        Notification::extend('smspro', function ($app) {
            return new SmsProChannel($app->make(SmsPro::class));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/smspro.php', 'smspro');

        $this->app->singleton(SmsPro::class, function ($app) {
            return new SmsPro(
                config('smspro.api_key'),
                config('smspro.sender_id'),
                config('smspro.base_url'),
                config('smspro.timeout', 30)
            );
        });

        $this->app->alias(SmsPro::class, 'smspro');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SmsPro::class, 'smspro'];
    }
}