<?php

namespace Mcms\Recipes;


use Mcms\Recipes\StartUp\RegisterAdminPackage;
use Mcms\Recipes\StartUp\RegisterEvents;
use Mcms\Recipes\StartUp\RegisterFacades;
use Mcms\Recipes\StartUp\RegisterMiddleware;
use Mcms\Recipes\StartUp\RegisterServiceProviders;
use Mcms\Recipes\StartUp\RegisterSettingsManager;
use Mcms\Recipes\StartUp\RegisterWidgets;
use Illuminate\Support\ServiceProvider;
use \App;
use \Installer, \Widget;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Routing\Router;

class RecipesServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        \Mcms\Recipes\Console\Commands\Install::class,
        \Mcms\Recipes\Console\Commands\RefreshAssets::class,
    ];
    
    public $packageName = 'package-recipes';
    
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(DispatcherContract $events, GateContract $gate, Router $router)
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('recipes.php'),
            __DIR__ . '/../config/recipe_settings.php' => config_path('recipe_settings.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../database/seeds/' => database_path('seeds')
        ], 'seeds');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/recipes'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang'),
        ], 'lang');

        $this->publishes([
            __DIR__ . '/../resources/public' => public_path('package-recipes'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('package-recipes'),
        ], 'assets');

        $this->publishes([
            __DIR__ . '/../config/admin.package.json' => storage_path('app/package-recipes/admin.package.json'),
        ], 'admin-package');
        

        if (!$this->app->routesAreCached()) {
            $router->group([
                'middleware' => 'web',
            ], function ($router) {
                require __DIR__.'/Http/routes.php';
            });

            $router->group([
                'prefix' => 'api',
                'middleware' => 'api',
            ], function ($router) {
                require __DIR__.'/Http/api.php';
            });

            $this->loadViewsFrom(__DIR__ . '/../resources/views', 'recipes');
        }



        /**
         * Register any widgets
         */
        (new RegisterWidgets())->handle();

        /**
         * Register Events
         */
//        parent::boot($events);
        (new RegisterEvents())->handle($this, $events);

        /*
         * Register dependencies
        */
        (new RegisterServiceProviders())->handle();

        /*
         * Register middleware
        */
        (new RegisterMiddleware())->handle($this, $router);


        /**
         * Register admin package
         */
        (new RegisterAdminPackage())->handle($this);

        (new RegisterSettingsManager())->handle($this);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        /*
        * Register Commands
        */
        $this->commands($this->commands);

        /**
         * Register Facades
         */
        (new RegisterFacades())->handle($this);


        /**
         * Register installer
         */
        Installer::register(\Mcms\Recipes\Installer\Install::class);

    }
}
