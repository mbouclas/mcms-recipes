<?php

namespace Mcms\Recipes\Presenters;
use App;
use Mcms\Core\Services\Presenter\Presenter;
use Mcms\Recipes\Models\Recipe;

/**
 * Works as $recipe->present()->methodName
 *
 * Class RecipePresenter
 * @package Mcms\Recipes\Presenters
 */
class RecipePresenter extends Presenter
{
    /**
     * @var string
     */
    protected $lang;

    /**
     * RecipePresenter constructor.
     * @param Recipe $recipe
     */
    public function __construct(Recipe $recipe)
    {
        $this->lang = App::getLocale();

        parent::__construct($recipe);
    }


}