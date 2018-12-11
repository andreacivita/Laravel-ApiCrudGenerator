<?php

namespace AndreaCivita\ApiCrudGenerator;

use Illuminate\Support\ServiceProvider;

class ApiCrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/stubs/' => base_path('resources/stubs/'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'AndreaCivita\ApiCrudGenerator\Commands\ApiCrudGenerator'
        );
    }
}
