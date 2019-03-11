<?php

namespace Mcms\Recipes\StartUp;

use Mcms\Core\Services\SettingsManager\SettingsManagerService;
use Illuminate\Support\ServiceProvider;

class RegisterSettingsManager
{
    public function handle(ServiceProvider $serviceProvider)
    {
        SettingsManagerService::register('recipes', 'recipe_settings.recipes');
        SettingsManagerService::register('recipeCategories', 'recipe_settings.categories');
    }
}