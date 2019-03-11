<?php

namespace Mcms\Recipes\Installer\AfterUpdate;


use Mcms\Core\Models\UpdatesLog;
use Illuminate\Console\Command;

class PublishMissingConfig
{
    public function handle(Command $command, UpdatesLog $item)
    {
        $item->result = true;
        $item->save();
        $command->comment('All done in PublishMissingConfig');
    }
}