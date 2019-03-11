(function() {
    'use strict';

    angular.module('mcms.recipes.recipe')
        .config(config);

    config.$inject = ['$routeProvider','RECIPES_CONFIG'];

    function config($routeProvider,Config) {

        $routeProvider
            .when('/recipes/content', {
                templateUrl:  Config.templatesDir + 'Recipe/index.html',
                controller: 'RecipeHomeController',
                controllerAs: 'VM',
                reloadOnSearch : true,
                resolve: {
                    init : ["AuthService", '$q', 'RecipeService', function (ACL, $q, Recipe) {
                        return (!ACL.level(2)) ? $q.reject(403) : Recipe.init();
                    }]
                },
                name: 'recipes-home'
            })
            .when('/recipes/content/:id', {
                templateUrl:  Config.templatesDir + 'Recipe/editRecipe.html',
                controller: 'RecipeController',
                controllerAs: 'VM',
                reloadOnSearch : false,
                resolve: {
                    item : ["AuthService", '$q', 'RecipeService', '$route', function (ACL, $q, Recipe, $route) {
                        return (!ACL.level(2)) ? $q.reject(403) : Recipe.find($route.current.params.id);
                    }]
                },
                name: 'recipes-edit'
            });
    }

})();
