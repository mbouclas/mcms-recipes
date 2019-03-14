<?php

namespace Mcms\Recipes\Models;

use Config;
use Mcms\Core\Models\ExtraFieldValue as BaseExtraFieldValue;


/**
 * Class Recipe
 * @package Mcms\Recipes\Models
 */
class ExtraFieldValue extends BaseExtraFieldValue
{
    protected $recipesModel;
    public $translatable = [];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->recipesModel = (Config::has('recipes.recipe')) ? Config::get('recipes.recipe') : Recipe::class;
    }

    public function field()
    {
        return $this->BelongsTo(ExtraField::class, 'extra_field_id');
    }

}
