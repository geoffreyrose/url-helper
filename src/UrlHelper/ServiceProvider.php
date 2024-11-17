<?php

namespace UrlHelper;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\UrlHelper\UrlHelper::class, function ($app) {
            return new \UrlHelper\UrlHelper();
        });

        $this->app->alias(\UrlHelper\UrlHelper::class, 'urlhelper');
    }
}
