(function(){
    'use strict';

    angular.module('mcms.recipes.recipeCategory', [
        'ui.tree'
    ])
        .run(run);

    run.$inject = ['mcms.menuService'];

    function run(Menu) {

    }


})();

require('./routes');
require('./dataService');
require('./service');
require('./RecipeCategoryHomeController');
require('./RecipeCategoryController');
require('./editRecipeCategory.component');