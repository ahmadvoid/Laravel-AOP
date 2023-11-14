<?php

namespace AhmadVoid\SimpleAOP;

use Illuminate\Support\ServiceProvider;

class AspectServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Bind the AspectHandler class to the app container as a singleton
        $this->app->singleton(AspectHandler::class, function ($app) {
            // Get an instance of the AttributeHandler class from the app container
            $attributeHandler = $app->make(AttributeHandler::class);

            // Return a new instance of the AspectHandler class with the AttributeHandler instance
            return new AspectHandler($attributeHandler);
        });

        // Bind the AttributeHandler class to the app container as a singleton
        $this->app->singleton(AttributeHandler::class, function ($app) {
            return new AttributeHandler();
        });

        // Alias the AspectHandler class as 'aspectHandler' for the facade
        $this->app->alias(AspectHandler::class, 'aspectHandler');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the Aspect facade
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Aspect', Aspect::class);
        });

        $this->commands([
            \AhmadVoid\SimpleAOP\Console\MakeAspect::class,
        ]);

        $this->publishes([
            __DIR__.'/aop.php' => config_path('aop.php'),
        ]);

        // Register the middleware with a short-hand key
        $this->app['router']->aliasMiddleware('aspect', AspectMiddleware::class);
    }
}
