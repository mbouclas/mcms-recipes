<?php
Route::group(['prefix' => 'admin/api'], function () {

    Route::group(['middleware' =>['level:2']], function($router)
    {
        $router->get('recipe/preview/{id}', 'Mcms\Recipes\Http\Controllers\RecipeController@preview');
        $router->resource('recipe' ,'Mcms\Recipes\Http\Controllers\RecipeController');
        $router->put('recipeCategory/rebuild','Mcms\Recipes\Http\Controllers\RecipeCategoryController@rebuild');
        $router->get('recipeCategory/tree','Mcms\Recipes\Http\Controllers\RecipeCategoryController@tree');
        $router->resource('recipeCategory' ,'Mcms\Recipes\Http\Controllers\RecipeCategoryController');
    });

});