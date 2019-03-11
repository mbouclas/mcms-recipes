<?php

namespace Mcms\Recipes\Console\Commands\InstallerActions;


use Illuminate\Console\Command;


/**
 * @example php artisan vendor:publish --provider="Mcms\Recipes\RecipesServiceProvider" --tag=config
 * Class PublishSettings
 * @package Mcms\Recipes\Console\Commands\InstallerActions
 */
class PublishSettings
{
    /**
     * @param Command $command
     */
    public function handle(Command $command)
    {
        $command->call('vendor:publish', [
            '--provider' => 'Mcms\Recipes\RecipesServiceProvider',
            '--tag' => ['config'],
        ]);

        $command->comment('* Settings published');
    }
}