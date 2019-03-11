(function () {
    'use strict';

    angular.module('mcms.recipes', [
        'mcms.mediaFiles',
        'mcms.fileGallery',
        'mcms.extraFields',
        'mcms.recipes.recipe',
        'mcms.recipes.recipeCategory',
        'mcms.recipes.extraFields',
        'ngFileUpload'
    ])

        .run(run);

    run.$inject = ['mcms.menuService', 'DynamicTableService', 'RECIPES_CONFIG'];

    function run(Menu, DynamicTableService, Config) {
        DynamicTableService.mapModel('recipes', Config.itemModelName);

        Menu.addMenu(Menu.newItem({
            id: 'recipes',
            title: 'Recipes',
            permalink: '',
            icon: 'fastfood',
            order: 1,
            acl: {
                type: 'level',
                permission: 2
            }
        }));

        var recipesMenu = Menu.find('recipes');

        recipesMenu.addChildren([
            Menu.newItem({
                id: 'recipesCategories-manager',
                title: 'Categories',
                permalink: '/recipes/categories',
                gate : 'cms.categories.menu',
                icon: 'view_list',
                order : 1
            }),
            Menu.newItem({
                id: 'recipes-manager',
                title: 'Recipes',
                permalink: '/recipes/content',
                icon: 'content_copy',
                order : 2
            }),
            Menu.newItem({
                id: 'recipes-extra-fields',
                title: 'Extra Fields',
                permalink: '/recipes/extraFields',
                gate : 'cms.extraFields.menu',
                icon: 'note_add',
                order : 3
            }),
            Menu.newItem({
                id: 'dynamic-tables',
                title: 'Dynamic Tables',
                permalink: '/dynamicTables/recipes',
                icon: 'assignment',
                order : 4
            })
        ]);
    }

})();

require('./config');
require('./Recipe');
require('./RecipeCategory');
require('./ExtraFields');
