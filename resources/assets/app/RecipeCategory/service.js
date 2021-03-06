(function () {
    'use strict';

    angular.module('mcms.recipes.recipeCategory')
        .service('RecipeCategoryService',Service);

    Service.$inject = ['RecipeCategoryDataService', 'ItemSelectorService', 'SeoService', 'LangService',
        'core.services', 'lodashFactory','mcms.settingsManagerService', '$q'];

    function Service(DS, ItemSelector, SEO, Lang, Helpers, lo, SM, $q) {
        var _this = this;
        var Categories = [];
        var CategoriesFlat = [];
        this.get = get;
        this.setCategories = setCategories;
        this.find = find;
        this.addCategory = addCategory;
        this.newCategory = newCategory;
        this.save = save;
        this.destroy = destroy;
        this.rebuild = rebuild;
        this.tree = tree;
        this.categories = categories;
        this.toFlat = flattenCategories;
        this.where = where;

        function get() {
            return DS.index()
                .then(function (response) {
                    setCategories(response)
                    return response;
                });
        }

        function setCategories(categories) {
            Categories = categories;
            CategoriesFlat = flattenCategories();
        }

        function find(id) {
            return DS.show(id)
                .then(function (response) {
                    ItemSelector.register(response.connectors);
                    SEO.init(response.seoFields);
                    SM.addSettingsItem(response.settings);
                    return response.item;
                });
        }

        function tree(filters) {
            return DS.tree(filters)
                .then(function (response) {
                    return response;
                });
        }

        function addCategory(parentId) {
            var newCat = newCategory();
            if (parentId && parseInt(parentId) !== 0) {
                newCat.parent_id = parentId;
                return find(parentId)
                    .then(function (parent) {
                       newCat.parent = parent;
                       return newCat;
                    });
            }

            return $q.resolve(newCat);
        }

        /**
         * Create the holder object for a new category object
         *
         * @returns {{title: string, description: string, slug: string, children: Array, settings: {}, active: boolean, orderBy: number}}
         */
        function newCategory() {
            var Locales = Lang.locales();
            var settings = {seo : {}};
            for (var key in Locales){
                settings.seo[key] = {};
            }

            return {
                title : Lang.langFields(),
                description : Lang.langFields(),
                slug : '',
                children : [],
                settings : settings,
                active : false,
                orderBy : 0,
            };
        }

        function save(item) {
            if (!item.id){
                return DS.store(item);
            }


            return DS.update(item);
        }

        function destroy(item) {
            return DS.destroy(item.id);
        }

        function rebuild(tree) {
            return DS.rebuild(tree)
                .then(function (newTree) {
                    Categories = newTree;
                });
        }

        function categories() {
            return Categories;
        }

        function flattenCategories() {
            CategoriesFlat = Helpers.flattenTree(Categories);
            return CategoriesFlat;
        }

        function where(search) {
            return lo.find(CategoriesFlat, search);
        }

    }
})();
