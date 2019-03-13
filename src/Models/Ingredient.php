<?php

namespace Mcms\Recipes\Models;
use Config;
use Mcms\FrontEnd\Helpers\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use Sluggable;
    protected $table = 'recipes_ingredients';
    protected $recipesModel;

    protected $fillable = [
        'title',
        'slug',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->recipesModel = (Config::has('recipes.recipe')) ? Config::get('recipes.recipe') : Recipe::class;
    }

    public function recipes()
    {
        return $this->belongsToMany($this->recipesModel);
    }
}
