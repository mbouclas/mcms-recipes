(function(){
    'use strict';

    angular.module('mcms.recipes.recipe')
        .directive('latestRecipesWidget', Component);

    Component.$inject = ['RECIPES_CONFIG', 'RecipeService'];

    function Component(Config, Recipe){

        return {
            templateUrl: Config.templatesDir + "Recipe/Widgets/latestRecipes.widget.html",
            restrict : 'E',
            scope : {
                options : '=?options'
            },
            link : function(scope, element, attrs, controllers){
                scope.Options = {limit : 5};
                if (typeof scope.options != 'undefined'){
                    scope.Options = angular.extend(scope.Options, scope.options);
                }

                Recipe.init({limit : scope.Options.limit}).then(function (res) {
                    scope.Categories = res[1];
                    scope.Items = res[0];

                });
            }
        };
    }
})();