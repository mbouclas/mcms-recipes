<?php

namespace Mcms\Recipes\Http\Controllers;

use App\Http\Controllers\Controller;
use Config;
use Mcms\Core\ExtraFields\ExtraFields;
use Mcms\Core\Models\Filters\ExtraFieldFilters;
use Mcms\Core\Services\DynamicTables\DynamicTablesService;
use Mcms\Core\Services\SettingsManager\SettingsManagerService;
use Mcms\Recipes\Models\Filters\RecipeFilters;
use Mcms\Recipes\Models\Recipe;
use Mcms\Recipes\Models\RecipeCategory;
use Mcms\Recipes\Services\Recipe\RecipeService;
use Illuminate\Http\Request;
use ItemConnector;

class RecipeController extends Controller
{
    protected $recipe;
    protected $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
        /*        $this->middleware('gate:cms.items.view',  ['only' => ['index', 'show', 'preview']]);
                $this->middleware('gate:cms.items.add',  ['only' => ['store', 'create', 'new']]);
                $this->middleware('gate:cms.items.delete',  ['only' => ['destroy']]);*/
    }

    public function index(RecipeFilters $filters, Request $request)
    {
        /*        $category = RecipeCategory::find(4);
                $category->children()->create([
                    'title' => ['en'=>'Jobs'],
                    'slug' => str_slug('Jobs'),
                    'description' => ['en'=>''],
                    'user_id' => \Auth::user()->id,
                    'orderBy' => 0,
                    'active' => true
                ]);*/
        /*        RecipeCategory::create([
                    'title' => ['en'=>'Properties'],
                    'slug' => str_slug('Properties'),
                    'description' => ['en'=>''],
                    'user_id' => \Auth::user()->id,
                    'orderBy' => 0,
                    'active' => true
                ]);*/

        /*        $recipe = Recipe::create([
                    'title' => ['en'=>'The team'],
                    'slug' => str_slug('The team'),
                    'description' => ['en'=>'sdfgs sgsg sdgsdg'],
                    'description_long' => ['en'=>'24rt243 tgf42 g432'],
                    'user_id' => \Auth::user()->id,
                    'active' => true
                ]);

                $category = RecipeCategory::find(4);

                $recipe->categories()->attach([$category->id, 5]);*/

//        return Recipe::with('categories')->find(129);

        /*        \DB::listen(function($sql) {
                    var_dump($sql->sql);
                    var_dump($sql->bindings);
                });*/

//        return Recipe::limit(10)->filter($filters)->get();
//        return RecipeCategory::with('recipes')->find(4);


//        return $recipeService->filter($filters);


        /*        $recipe = $recipeService->store([
                    'title' => 'a new recipe',
                    'slug' => str_slug('a new recipe'),
                    'active' => true,
                    'user_id' => 2,
                    'categories' => [
                        ['id'=>3],
                        ['id'=>4,'main'=>true]
                    ]
                ]);*/


        /*        $recipe = Recipe::with('categories')->find(109);
                $update = $recipe->toArray();
                $update['categories'] = [
                    ['id'=>4],
                    ['id'=>5,'main'=>true]
                ];
                $recipe = $recipeService->update($recipe->id, $update);*/

        \DB::listen(function ($query) {
//            print_r($query->sql);
//            print_r($query->bindings);
            // $query->time
        });
        $limit = ($request->has('limit')) ? (int)$request->input('limit') : 10;

        if (! $request->has('orderBy')) {
            $request->merge(['orderBy' => 'created_at']);
        }

        return $this->recipeService->model->with(['categories', 'images'])
            ->filter($filters)
            ->paginate($limit);
    }

    public function store(Request $request)
    {
        $data = $request->toArray();
        $data['user_id'] = \Auth::user()->id;
        return $this->recipeService->store($data);
    }


    public function update(Request $request, $id)
    {
        return $this->recipeService->update($id, $request->toArray());
    }


    public function destroy($id)
    {
        $result = $this->recipeService->destroy($id);
        return ['success' => $result];
    }

    public function show($id, ExtraFieldFilters $filters)
    {

        $imageCategories = Config::get('recipes.items.images.types');
        $extraFieldService = new ExtraFields();
        \DB::listen(function ($query) {
//            print_r($query->sql);
//            print_r($query->bindings);
            // $query->time
        });
        $filters->request->merge(['model' => str_replace('\\', '\\\\', get_class($this->recipeService->model))]);
        $dynamicTableService = new DynamicTablesService(new $this->recipeService->model->dynamicTablesModel);



        return [
            'item' => $this->recipeService->findOne($id, ['related', 'categories', 'dynamicTables',
                'galleries','tagged','files', 'extraFields', 'extraFields.field']),
            'imageCategories' => $imageCategories,
            'extraFields' => $extraFieldService->model->filter($filters)->get(),
            'imageCopies' => Config::get('recipes.items.images'),
            'config' => Config::get('recipes.items'),
            'tags' => $this->recipeService->model->existingTags(),
            'dynamicTables' => $dynamicTableService->all(),
            'settings' => SettingsManagerService::get('recipes'),
            'connectors' => ItemConnector::connectors(),
            'seoFields' => Config::get('seo')
        ];
    }

    public function preview($id)
    {
        $item = Recipe::find($id);
        return response(['url' => $item->createUrl()]);
    }
}
