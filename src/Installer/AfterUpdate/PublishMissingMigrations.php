<?php

namespace Mcms\Recipes\Installer\AfterUpdate;


use Mcms\Core\Models\UpdatesLog;
use Illuminate\Console\Command;

class PublishMissingMigrations
{
    public function handle(Command $command, UpdatesLog $item)
    {
        $item->result = true;
        $item->save();
        $command->comment('All done in PublishMissingMigrations');
    }
}