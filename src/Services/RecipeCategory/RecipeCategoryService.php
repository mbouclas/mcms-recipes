<?php

namespace Mcms\Recipes\Services\RecipeCategory;


use App;
use Config;
use Event;
use Mcms\Core\Helpers\Strings;
use Mcms\FrontEnd\Services\PermalinkArchive;
use Mcms\Recipes\Exceptions\InvalidRecipeCategoryFormatException;
use Mcms\Recipes\Models\RecipeCategory;
use Mcms\Recipes\Services\Recipe\RecipeCategoryValidator;
use Illuminate\Support\Collection;

/**
 * Class RecipeCategoryService
 * @package Mcms\Recipes\Services\RecipeCategory
 */
class RecipeCategoryService
{
    /**
     * @var RecipeCategory
     */
    protected $category;
    /**
     * @var
     */
    public $model;

    /**
     * @var RecipeCategoryValidator
     */
    protected $validator;



    /**
     * RecipeService constructor.
     */
    public function __construct()
    {
        $this->category = $this->model = new RecipeCategory;
        $this->validator = new RecipeCategoryValidator();
    }

    /**
     * @param $id
     * @param array $category
     * @return array
     */
    public function update($id, array $category)
    {
        $Category = $this->category->find($id);
        if ($Category->slug != $category['slug']){
            //create link
            $newLink = $this->model->generateSlug($category);
            //write 301
            PermalinkArchive::add($this->model->generateSlug($Category->toArray()), $newLink);
        }
        $Category->update($category);
        //sanitize the model
        $Category = $this->saveFeatured($category, $Category);

        //emit an event so that some other bit of the app might catch it
        event('menu.item.sync',$Category);

        return $Category;
    }

    /**
     * Create a new category
     *
     * @param array $category
     * @return static
     */
    public function store(array $category, $parentId = null)
    {
        try {
            $this->validator->validate($category);
        }
        catch (InvalidRecipeCategoryFormatException $e){
            return $e->getMessage();
        }

        $category['slug'] = $this->setSlug($category);

        //first check for parent. If no parent given, this is a root item
        if ( ! $parentId){
            return $this->category->create($category);
        }

        //find the parent
        $parent = $this->model->find($parentId);

        $newCategory = $parent->children()->create($category);

        $newCategory = $this->saveFeatured($category, $newCategory);
        return $newCategory;
    }

    /**
     * Delete a category
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $item = $this->category->find($id);
        //emit an event so that some other bit of the app might catch it
        event('menu.item.destroy',$item);
        return $item->delete();
    }

    private function setSlug($item){
        if ( ! isset($item['slug']) || ! $item['slug']){
            return str_slug($item['title'][App::getLocale()]);
        }

        return $item['slug'];
    }

    /**
     * @param array $category
     * @param $Category
     * @return RecipeCategory
     */
    private function saveFeatured(array $category, RecipeCategory $Category)
    {
        if ( ! isset($category['featured'])){
            return $Category;
        }

        foreach ($category['featured'] as $index => $item) {
            $category['featured'][$index]['model'] = get_class($Category);
        }

        $Category->featured = $Category->saveFeatured($category['featured']);

        return $Category;
    }

    public function buildPermalink(array $item)
    {
        $stringHelpers = new Strings();

        return $stringHelpers->vksprintf(Config::get('recipes.categories.slug_pattern'), $item);
    }

    public function htmlTree($parent = null)
    {
        $leafs = new Collection();
        $results = $this->model;
        if ($parent) {
            $results = $results->where('parent_id', $parent);
        }

        $results = $results->defaultOrder()
            ->get()
            ->toTree();

        $traverse = function ($categories, $prefix = '-') use (&$traverse, $leafs) {
            foreach ($categories as $category) {
                $space = '';
                for ($i = 0; strlen($prefix) > $i; $i++) {
                    $space .= '&nbsp;&nbsp;';
                }

                $leafs->push([
                    'id' => $category->id,
                    'label' => $space . ' ' . $prefix . ' ' . $category->title,
                    'title' => $category->title
                ]);

                $traverse($category->children, $prefix . '-');
            }

            return $leafs;
        };

        $tree = $traverse($results);

        return $tree;
    }
}