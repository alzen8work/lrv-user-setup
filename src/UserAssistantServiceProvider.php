<?php

namespace alzen8work\UserAssistant;

use alzen8work\UserAssistant\Commands\UserSetup;
use Illuminate\Support\ServiceProvider;

class UserAssistantServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'alzen8work');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'alzen8work');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
            $this->commands([
                UserSetup::class
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/userassistant.php', 'userassistant');

        // Register the service the package provides.
        $this->app->singleton('userassistant', function ($app) {
            return new UserAssistant;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['userassistant'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/userassistant.php' => config_path('userassistant.php'),
        ], 'userassistant.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/alzen8work'),
        ], 'dockerassistant.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/alzen8work'),
        ], 'dockerassistant.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/alzen8work'),
        ], 'dockerassistant.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
