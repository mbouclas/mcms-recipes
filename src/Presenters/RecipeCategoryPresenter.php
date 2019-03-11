<?php

namespace Mcms\Recipes\Presenters;
use App;
use Mcms\Core\Services\Presenter\Presenter;
use Mcms\Recipes\Models\RecipeCategory;

/**
 * Works as $category->present()->methodName
 *
 * Class RecipeCategoryPresenter
 * @package Mcms\Recipes\Presenters
 */
class RecipeCategoryPresenter extends Presenter
{
    /**
     * @var string
     */
    protected $lang;

    /**
     * RecipePresenter constructor.
     * @param RecipeCategory $recipeCategory
     */
    public function __construct(RecipeCategory $recipeCategory)
    {
        $this->lang = App::getLocale();

        parent::__construct($recipeCategory);
    }


}