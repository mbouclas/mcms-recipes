(function() {
    'use strict';

    angular.module('mcms.recipes.recipeCategory')
        .config(config);

    config.$inject = ['$routeProvider','RECIPES_CONFIG'];

    function config($routeProvider,Config) {

        $routeProvider
            .when('/recipes/categories', {
                templateUrl:  Config.templatesDir + 'RecipeCategory/index.html',
                controller: 'RecipeCategoryHomeController',
                controllerAs: 'VM',
                reloadOnSearch : false,
                resolve: {
                    init : ["AuthService", '$q', 'RecipeCategoryService', function (ACL, $q, Category) {
                        return (!ACL.inGates('cms.categories.menu')) ? $q.reject(403) : Category.get();
                    }]
                },
                name: 'recipes-categories'
            })
            .when('/recipes/categories/:id', {
                templateUrl:  Config.templatesDir + 'RecipeCategory/edit.html',
                controller: 'RecipeCategoryController',
                controllerAs: 'VM',
                reloadOnSearch : false,
                resolve: {
                    init : ["AuthService", '$q', 'RecipeCategoryService', '$route', function (ACL, $q, Category, $route) {
                        return (!ACL.inGates('cms.categories.menu')) ? $q.reject(403) : Category.find($route.current.params.id);
                    }]
                },
                name: 'recipes-category'
            })
            .when('/recipes/categories/add/:parentId', {
                templateUrl:  Config.templatesDir + 'RecipeCategory/edit.html',
                controller: 'RecipeCategoryController',
                controllerAs: 'VM',
                reloadOnSearch : false,
                resolve: {
                    init : ["AuthService", '$q', '$route', 'RecipeCategoryService', function (ACL, $q, $route, RecipeCategoryService) {
                        return (!ACL.inGates('cms.categories.menu') ? $q.reject(403) : RecipeCategoryService.addCategory($route.current.params.parentId));
                    }]
                },
                name: 'recipes-new-category'
            });
    }
})();
