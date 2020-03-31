<?php

namespace Codtail\Translation;

use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishable();
        }

        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../routes.php';
        }
    }

    public function register()
    {

    }

    protected function publishable()
    {
        $this->publishes([
            __DIR__.'/../config/translation.php' => config_path('translation.php')
        ], 'translation-config');   
    }
}