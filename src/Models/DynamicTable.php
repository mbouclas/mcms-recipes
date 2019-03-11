<?php

namespace Mcms\Recipes\Models;


use Config;
use Mcms\Core\Models\DynamicTable as BaseDynamicTable;
use Mcms\FrontEnd\Helpers\Sluggable;

class DynamicTable extends BaseDynamicTable
{
    use Sluggable;

    public $itemModel;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->itemModel = (Config::has('recipes.recipe')) ? Config::get('recipes.recipe') : Recipe::class;
    }

    public function recipes()
    {
        return $this->belongsToMany($this->itemModel);
    }


}
