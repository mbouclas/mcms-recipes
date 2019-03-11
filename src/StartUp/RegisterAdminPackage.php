<?php

namespace Mcms\Recipes\StartUp;


use Mcms\Recipes\Menu\RecipesInterfaceMenuConnector;
use Mcms\Recipes\Models\Recipe;
use Illuminate\Support\ServiceProvider;
use ModuleRegistry, ItemConnector;

class RegisterAdminPackage
{
    public function handle(ServiceProvider $serviceProvider)
    {
        ModuleRegistry::registerModule($serviceProvider->packageName . '/admin.package.json');
        try {
            ItemConnector::register((new RecipesInterfaceMenuConnector())->run()->toArray());
        } catch (\Exception $e){

        }
    }
}