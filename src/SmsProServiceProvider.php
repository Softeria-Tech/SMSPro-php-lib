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
            // Publish config file
            $this->publishes([
                __DIR__ . '/../config/smspro.php' => config_path('smspro.php'),
            ], 'smspro-config');

            // Publish config file with tag 'config' for Laravel's default behavior
            $this->publishes([
                __DIR__ . '/../config/smspro.php' => config_path('smspro.php'),
            ], 'config');

            // If the config file doesn't exist, create it
            if (!file_exists(config_path('smspro.php'))) {
                $this->createConfigFile();
            }
        }

        // Register the notification channel
        Notification::extend('smspro', function ($app) {
            return new SmsProChannel($app->make(SmsPro::class));
        });

        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../config/smspro.php', 'smspro');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
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

    /**
     * Create the config file if it doesn't exist
     *
     * @return void
     */
    protected function createConfigFile()
    {
        $configContent = '<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | This is your API key from SMSPro platform. You can find it in your
    | account dashboard.
    |
    */
    \'api_key\' => env(\'SMSPRO_API_KEY\', \'\'),

    /*
    |--------------------------------------------------------------------------
    | Sender ID
    |--------------------------------------------------------------------------
    |
    | This is your registered sender ID from SMSPro platform.
    | Leave empty to use the default sender ID.
    |
    */
    \'sender_id\' => env(\'SMSPRO_SENDER_ID\', \'\'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the SMSPro API.
    |
    */
    \'base_url\' => env(\'SMSPRO_BASE_URL\', \'https://sms.softeriatech.com/api/v1/bulksms\'),

    /*
    |--------------------------------------------------------------------------
    | Default Country Code
    |--------------------------------------------------------------------------
    |
    | Default country code to prepend to phone numbers if not provided.
    |
    */
    \'default_country_code\' => env(\'SMSPRO_DEFAULT_COUNTRY_CODE\', \'254\'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Request timeout in seconds.
    |
    */
    \'timeout\' => env(\'SMSPRO_TIMEOUT\', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Attempts
    |--------------------------------------------------------------------------
    |
    | Number of retry attempts for failed requests.
    |
    */
    \'retry_attempts\' => env(\'SMSPRO_RETRY_ATTEMPTS\', 3),

    /*
    |--------------------------------------------------------------------------
    | Log Channel
    |--------------------------------------------------------------------------
    |
    | The log channel to use for logging SMS activities.
    |
    */
    \'log_channel\' => env(\'SMSPRO_LOG_CHANNEL\', \'daily\'),
];
';
        file_put_contents(config_path('smspro.php'), $configContent);
    }
}