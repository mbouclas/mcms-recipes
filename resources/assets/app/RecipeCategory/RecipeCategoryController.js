(function() {
    'use strict';

    angular.module('mcms.recipes.recipeCategory')
        .controller('RecipeCategoryController',Controller);

    Controller.$inject = ['init', 'LangService', 'core.services'];

    function Controller(Category, Lang, Helpers) {
        var vm = this;
        vm.Category = Category;
        vm.defaultLang = Lang.defaultLang();

        vm.onSave = function(item, isNew, parent){
            if (isNew) {
                return Helpers.redirectTo('recipes-category', {id : item.id});
            }
        }
    }

})();
