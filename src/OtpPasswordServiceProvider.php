<?php

namespace SadiqSalau\LaravelOtpPassword;


use Illuminate\Support\ServiceProvider;
use SadiqSalau\LaravelOtpPassword\OtpPasswordBrokerManager;

class OtpPasswordServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('auth.password.otp', function ($app) {
            return new OtpPasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.otp.broker', function ($app) {
            return $app->make('auth.password.otp')->broker();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
