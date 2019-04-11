<?php

namespace Mcms\Recipes\Services\Recipe;


use App;
use Config;
use Event;
use Mcms\Core\Helpers\Strings;
use Mcms\Core\Models\Image;
use Mcms\Core\Models\MenuItem;

use Mcms\Core\QueryFilters\Filterable;
use Mcms\Core\Services\Image\GroupImagesByType;
use Mcms\Core\Traits\FixTags;
use Mcms\FrontEnd\Services\PermalinkArchive;
use Mcms\Recipes\Exceptions\InvalidRecipeFormatException;
use Mcms\Recipes\Models\Featured;
use Mcms\Recipes\Models\Recipe;
use Mcms\Recipes\Models\Related;

/**
 * Class RecipeService
 * @package Mcms\Recipes\Services\Recipe
 */
class RecipeService
{
    use Filterable, FixTags;

    /**
     * @var Recipe
     */
    protected $recipe;
    /**
     * @var
     */
    public $model;

    protected $validator;

    protected $imageGrouping;

    /**
     * RecipeService constructor.
     * @param Recipe $recipe
     */
    public function __construct()
    {
        $model = (Config::has('recipes.recipe')) ? Config::get('recipes.recipe') : Recipe::class;
        $this->recipe = $this->model = new $model;
        $this->validator = new RecipeValidator();
        $this->imageGrouping = new GroupImagesByType();
    }

    /**
     * Filters the translations based on filters provided
     * Legend has it that it will filter properly role based queries.
     * So, if i am an admin, i should not be able to see the super users
     *
     * @param $filters
     */

    public function filter($filters, array $options = [])
    {
        $results = $this->recipe->filter($filters);
        $results = (array_key_exists('orderBy', $options)) ? $results->orderBy($options['orderBy']) : $results->orderBy('created_at', 'asc');
        $limit = ($filters->request->has('limit')) ? $filters->request->input('limit') : 10;
        $results = $results->paginate($limit);


        return $results;
    }

    /**
     * @param $id
     * @param array $recipe
     * @return array
     */
    public function update($id, array $recipe)
    {
        $Recipe = $this->recipe->find($id);
        //link has changed, write it out as a 301
        //create link
        $oldLink = $Recipe->generateSlug();
        $newLink = $Recipe->generateSlug($recipe);

        if ($oldLink != $newLink){
            //write 301

            PermalinkArchive::add($oldLink, $newLink);
        }
        $Recipe->update($recipe);
        //update relations
        $Recipe->categories()->sync($this->sortOutCategories($recipe['categories']));
        //sanitize the model
        $Recipe = $this->saveRelated($recipe, $Recipe);

        $Recipe = $this->fixTags($recipe, $Recipe);
        if (isset($recipe['extra_fields'])){
            $Recipe->extraFieldValues()->sync($Recipe->sortOutExtraFields($recipe['extra_fields']));
        }

        //emit an event so that some other bit of the app might catch it
        event('menu.item.sync',$Recipe);
        event('recipe.updated',$Recipe);

        return $Recipe;
    }

    /**
     * Create a new recipe
     *
     * @param array $recipe
     * @return static
     */
    public function store(array $recipe)
    {
        try {
            $this->validator->validate($recipe);
        }
        catch (InvalidRecipeFormatException $e){
            return $e->getMessage();
        }

        $recipe['slug'] = $this->setSlug($recipe);

        $Recipe = $this->recipe->create($recipe);
        $Recipe->categories()->attach($this->sortOutCategories($recipe['categories']));
        $Recipe = $this->saveRelated($recipe, $Recipe);
        $Recipe = $this->fixTags($recipe, $Recipe);
        event('recipe.created',$Recipe);
        return $Recipe;
    }

    /**
     * Delete a recipe
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $item = $this->recipe->find($id);
        //delete images
        Image::where('model',get_class($this->model))->where('item_id', $id)->delete();
        //delete from menus
        MenuItem::where('model',get_class($this->model))->where('item_id', $id)->delete();
        //delete from featured
        Featured::where('model',get_class($this->model))->where('item_id', $id)->delete();
        //delete from related
        Related::where('model',get_class($this->model))->where('source_item_id', $id)->orWhere('item_id', $id)->delete();
        //emit an event so that some other bit of the app might catch it
        event('menu.item.destroy',$item);
        event('recipe.destroyed',$item);

        return $item->delete();
    }

    public function findOne($id, array $with = [], array $options = [
        'where' => []
    ])
    {

        $item = $this->model
            ->with($with);

        if (count($options['where']) > 0) {
            foreach ($options['where'] as $key => $value) {
                $item = $item->where($key, $value);
            }
        }

        $item = $item->find($id);

        if ($item){
            $item = $item->relatedItems();
            $item->related = collect($item->related);

            if (in_array('galleries', $with)){
                $item->images = $this->imageGrouping
                    ->group($item->galleries, \Config::get('recipes.items.images.types'));
            }

        }


        return $item;
    }

    /**
     * create an array of category ids with the extra value main
     *
     * @param $itemCategories
     * @return array
     */
    private function sortOutCategories($itemCategories){
        $categories = [];
        foreach ($itemCategories as $category){
            $main = (! isset($category['main']) || ! $category['main']) ? false : true;
            $categories[$category['id']] = ['main' => $main];
        }

        return $categories;
    }

    private function setSlug($item){
        if ( ! isset($item['slug']) || ! $item['slug']){
            return str_slug($item['title'][App::getLocale()]);
        }

        return $item['slug'];
    }


    /**
     * @param array $recipe
     * @param Recipe $Recipe
     * @return Recipe
     */
    private function saveRelated(array $recipe, Recipe $Recipe)
    {
        if ( ! isset($recipe['related']) || ! is_array($recipe['related'])  ){
            return $Recipe;
        }

        foreach ($recipe['related'] as $index => $item) {
            $recipe['related'][$index]['dest_model'] = ( ! isset($item['dest_model']))
                ? $recipe['related'][$index]['dest_model'] = $item['model']
                : $recipe['related'][$index]['dest_model'] = $item['dest_model'];
            $recipe['related'][$index]['model'] = get_class($Recipe);
        }

        $Recipe->related = $Recipe->saveRelated($recipe['related']);

        return $Recipe;
    }

    public function buildPermalink(array $item)
    {
        $stringHelpers = new Strings();

        return $stringHelpers->vksprintf(Config::get('recipes.items.slug_pattern'), $item);
    }


}