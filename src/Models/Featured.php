<?php

namespace Mcms\Recipes\Models;

use Config;
use Mcms\Core\Models\Featured as BaseFeatured;


/**
 * Class Recipe
 * @package Mcms\Recipes\Models
 */
class Featured extends BaseFeatured
{

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    protected $recipesModel;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->recipesModel = (Config::has('recipes.recipe')) ? Config::get('recipes.recipe') : Recipe::class;
    }

    public function item()
    {
        return $this->BelongsTo($this->recipesModel, 'item_id');
    }

}
