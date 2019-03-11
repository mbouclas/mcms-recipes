(function(){
    'use strict';

    angular.module('mcms.recipes.recipe', [
        'cfp.hotkeys'
    ])
        .run(run);

    run.$inject = ['mcms.widgetService'];

    function run(Widget) {
        Widget.registerWidget(Widget.newWidget({
            id : 'latestRecipes',
            title : 'Latest recipes',
            template : '<latest-recipes-widget></latest-recipes-widget>',
            settings : {},
            order : 10
        }));

    }
})();

require('./routes');
require('./dataService');
require('./service');
require('./RecipeHomeController');
require('./RecipeController');
require('./recipeList.component');
require('./editRecipe.component');
require('./Widgets/latestRecipes.widget');
