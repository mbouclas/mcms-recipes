<?php

namespace Mcms\Recipes\StartUp;



use Mcms\Recipes\Middleware\PublishRecipe;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Class RegisterMiddleware
 * @package Mcms\Recipes\StartUp
 */
class RegisterMiddleware
{

    /**
     * Register all your middleware here
     * @param ServiceProvider $serviceProvider
     * @param Router $router
     */
    public function handle(ServiceProvider $serviceProvider, Router $router)
    {
        $router->aliasMiddleware('publishRecipe', PublishRecipe::class);
    }
}