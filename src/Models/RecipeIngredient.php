<?php

namespace Mcms\Recipes\Models;


use Config;
use Illuminate\Database\Eloquent\Model;
use Mcms\FrontEnd\Helpers\Sluggable;

class RecipeIngredient extends Model
{
    use Sluggable;
    protected $table = 'recipe_recipe_ingredient';
    protected $recipesModel;

    protected $fillable = [
        'title',
        'main',
        'unit_id',
        'quantity',
        'type',
        'orderBy',
        'recipe_ingredient_id',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->recipesModel = (Config::has('recipes.recipe')) ? Config::get('recipes.recipe') : Recipe::class;
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'recipe_ingredient_id');
    }

    public function recipes()
    {
        return $this->belongsToMany($this->recipesModel);
    }
}