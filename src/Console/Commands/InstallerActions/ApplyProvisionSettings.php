<?php

namespace Mcms\Recipes\Console\Commands\InstallerActions;


use Illuminate\Console\Command;

class ApplyProvisionSettings
{
    public function handle(Command $command, $provisionFile)
    {
        $command->comment("* Provision file {$provisionFile} applied to the application");
    }
}