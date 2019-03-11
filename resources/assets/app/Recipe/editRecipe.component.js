(function () {
    angular.module('mcms.recipes.recipe')
        .directive('editRecipe', Directive);

    Directive.$inject = ['RECIPES_CONFIG', 'hotkeys'];
    DirectiveController.$inject = [ '$scope','RecipeService',
        'core.services', 'configuration', 'AuthService', 'LangService',
        'RecipeCategoryService',  'RECIPES_CONFIG', 'ItemSelectorService', 'lodashFactory',
        'mcms.settingsManagerService', 'SeoService', 'LayoutManagerService', '$timeout', '$rootScope', '$q',
        'momentFactory', 'ModuleExtender', 'MediaLibraryService', 'ExtraFieldService'];

    function Directive(Config, hotkeys) {

        return {
            templateUrl: Config.templatesDir + "Recipe/editRecipe.component.html",
            controller: DirectiveController,
            controllerAs: 'VM',
            require : ['editRecipe'],
            scope: {
                options: '=?options',
                item: '=?item',
                onSave : '&?onSave'
            },
            restrict: 'E',
            link: function (scope, element, attrs, controllers) {
                var defaults = {
                    hasFilters: true,
                    isWindow : false
                };

                scope.refreshIframe = function () {
                    var iframe = document.getElementById('preview');
                    if (!iframe){
                        return;
                    }

                    iframe.contentDocument.location.reload(true);
                };

                hotkeys.add({
                    combo: 'ctrl+s',
                    description: 'save',
                    allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
                    callback: function(e) {
                        e.preventDefault();
                        controllers[0].save();
                    }
                });

                controllers[0].init(scope.item);
                scope.options = (!scope.options) ? defaults : angular.extend(defaults, scope.options);
            }
        };
    }

    function DirectiveController($scope, Recipe, Helpers, Config, ACL, Lang, RecipeCategory, RecipesConfig,
                                 ItemSelector, lo, SM, SEO, LMS, $timeout, $rootScope, $q,
                                 moment, ModuleExtender, MLS, ExtraFieldService) {

        var vm = this,
            autoSaveHooks = [],
            Model = '\\Mcms\\Recipes\\Models\\Recipe';

        vm.published_at = {};
        vm.Lang = Lang;
        vm.defaultLang = Lang.defaultLang();
        vm.Locales = Lang.locales();
        vm.ValidationMessagesTemplate = Config.validationMessages;
        vm.Roles = ACL.roles();
        vm.Item = {};
        vm.Roles = ACL.roles();
        vm.Permissions = ACL.permissions();
        vm.isSu = ACL.role('su');//more efficient check
        vm.isAdmin = ACL.role('admin');//more efficient check
        vm.userSelectorOptions = {gate:'cms.items.add'};

        vm.tabs = [
            {
                label : 'General',
                file : RecipesConfig.templatesDir + 'Recipe/Components/tab-general-info.html',
                active : true,
                default : true,
                id : 'general',
                order : 1
            },
            {
                label : 'Translations',
                file : RecipesConfig.templatesDir + 'Recipe/Components/tab-translations.html',
                active : false,
                id : 'translations',
                order : 20
            },
            {
                label : 'Image gallery',
                file : RecipesConfig.templatesDir + 'Recipe/Components/tab-image-gallery.html',
                active : false,
                default : false,
                id : 'imageGallery',
                order : 30
            },
            {
                label : 'Files',
                file : RecipesConfig.templatesDir + 'Recipe/Components/tab-file-gallery.html',
                active : false,
                default : false,
                id : 'fileGallery',
                order : 40
            },
            {
                label : 'Extra Fields',
                file : RecipesConfig.templatesDir + 'Recipe/Components/tab-extra-fields.html',
                active : false,
                id : 'extraFields',
                order : 45
            },
            {
                label : 'Related Items',
                file : RecipesConfig.templatesDir + 'Recipe/Components/tab-related-items.html',
                active : false,
                id : 'related',
                order : 50
            },
            {
                label : 'SEO',
                file : RecipesConfig.templatesDir + 'Recipe/Components/tab-seo.html',
                active : false,
                id : 'seo',
                order : 60
            }
        ];

        vm.tabs = ModuleExtender.extend('recipes', vm.tabs);
        if (Lang.allLocales().length == 1){
            //remove the translation tab
            var tabIndex = lo.findIndex(vm.tabs, {id : 'translations'});
            vm.tabs.splice(tabIndex, 1);
        }
        vm.Categories = [];
        vm.thumbUploadOptions = {
            url : Config.imageUploadUrl,
            acceptSelect : RecipesConfig.fileTypes.image.acceptSelect,
            maxFiles : 1,
            params : {
                container : 'Item'
            }
        };

        vm.imagesUploadOptions = {
            url : RecipesConfig.imageUploadUrl,
            acceptSelect : RecipesConfig.fileTypes.image.acceptSelect,
            params : {
                container : 'Item'
            },
            uploadOptions : RecipesConfig.fileTypes.image.uploadOptions
        };
        vm.mediaFilesOptions = {imageTypes : [], withMediaLibrary : true};
        vm.UploadConfig = {
            file : {},
            image : vm.imagesUploadOptions,
        };

        vm.FileUploadConfig = {
            url : Config.fileUploadUrl,
            acceptedFiles : RecipesConfig.fileTypes.file.acceptSelect,
            uploadOptions : RecipesConfig.fileTypes.file.uploadOptions,
            params : {
                container : 'Item'
            }
        };

        vm.Layouts = LMS.layouts('recipes.items');
        vm.LayoutsObj = LMS.toObj();
        vm.categoriesValid = null;

        vm.init = function (item) {
            if (!item.id){
                //call for data from the server
                return Recipe.find(item)
                    .then(init);
            }

            init(item);

        };


        vm.exists = function (item, type) {
            type = (!type) ? 'checkForPermission' : 'checkFor' + type;
            return ACL[type](vm.User, item);
        };

        vm.save = function () {
            vm.errorsFound = false;
            if (typeof $scope.ItemForm == 'undefined'){
                return $q.reject();
            }

            if (!$scope.ItemForm.$valid){

                Helpers.toast($scope.ItemForm.$error.required.length + ' Errors found, please fill all required fields', null, 5000, 'error');
                vm.errorsFound = true;

                $rootScope.$broadcast('scroll.to.top');
                return $q.reject();
            }

            var isNew = (!(typeof vm.Item.id == 'number'));

            vm.Item.published_at = Helpers.deComposeDate(vm.publish_at).toISOString();


            return Recipe.save(vm.Item)
                .then(function (result) {
                   Helpers.toast('Saved!', null, null, 'success');

                    if (isNew){
                        vm.Item = result;
                    }

                    if (typeof $scope.onSave == 'function'){
                        $scope.onSave({item : result, isNew : isNew});
                    }

                    return result;
                });
        };

        vm.onResult = function (result) {
            if (typeof vm.Item.related == 'undefined' || !vm.Item.related){
                vm.Item.related = [];
            }

            result.source_item_id = vm.Item.id;
            vm.Item.related.push(result);
        };

        vm.removeCategory = function (cat) {
            vm.Item.categories.splice(lo.findIndex(vm.Item.categories, {id : cat.id}), 1);

            if (vm.Item.categories.length == 0){
                vm.categoriesValid = null;
            }
        };

        vm.onCategorySelected = function (cat) {
            if (typeof vm.Item.categories == 'undefined') {
                vm.Item.categories = [];
            }

            if (!cat || typeof cat.id == 'undefined'){
                return;
            }

            if (lo.find(vm.Item.categories, {id : cat.id})){
                return;
            }

            vm.Item.categories.push(cat);
            vm.categoriesValid = true;
            vm.searchText = null;
        };

        vm.getCategories = function (query) {

            if (vm.Categories.length > 0){
                return (!query) ? vm.Categories : vm.Categories.filter( Helpers.createFilterFor('title',query) );
            }

            return RecipeCategory.tree()
                .then(function (res) {
                    vm.Categories = res;
                    return (!query) ? res : res.filter( Helpers.createFilterFor('title',query) );
                });
        };

        function init(item) {
            vm.Item = item;

            if (typeof vm.Item.files == 'undefined'){
                vm.Item.files = [];
            }
            if (!lo.isObject(vm.Item.extra_fields)){
                vm.Item.extra_fields = {};
            }
            vm.Item.extra_fields = ExtraFieldService.simplifyFromMysql(item.extra_fields);

            SEO.fillFields(vm.Item.settings, function (model, key) {
                SEO.prefill(model, vm.Item, key);
            });
            // console.log(lo.find(vm.Layouts, {varName : vm.Item.settings.Layout.id}));
            vm.publish_at = Helpers.composeDate(vm.Item.published_at);
            vm.SEO = SEO.fields();
            vm.Connectors = ItemSelector.connectors();
            vm.thumbUploadOptions.params.item_id = item.id;
            vm.thumbUploadOptions.params.model = Model;
            vm.thumbUploadOptions.params.type = 'thumb';
            vm.thumbUploadOptions.params.resize = true;
            vm.imagesUploadOptions.params.item_id = item.id;
            vm.imagesUploadOptions.params.model = Model;
            vm.imagesUploadOptions.params.type = 'images';
            vm.imagesUploadOptions.params.resize = true;
            vm.FileUploadConfig.params.item_id = item.id;
            vm.FileUploadConfig.params.model = Model;
            vm.FileUploadConfig.params.type = 'file';
            LMS.setModel(vm.Item);
            vm.Settings = SM.get({name : 'recipes'});
            if (lo.isArray(vm.Item.categories) && vm.Item.categories.length > 0){
                vm.categoriesValid = true;
            }

            vm.filterExtraFields();
            vm.adminSize = Recipe.imageSettings().adminCopy();
            vm.recommendedSizeLabel = Recipe.imageSettings().recommendedSizeLabel();
        }

        vm.filterExtraFields = function() {
            var layout = (typeof vm.Item.settings.Layout.id != 'undefined') ? vm.Item.settings.Layout.id : null;

            vm.ExtraFields = ExtraFieldService
                .filter(Recipe.extraFields())
                .whereIn('layoutId', layout);
        };



        var watcher = null,
            timer = null;

        /*
        * autosave
        * */
        watcher = $scope.$watch(angular.bind(vm, function () {
            var publishDate = Helpers.deComposeDate(vm.publish_at);
            if (publishDate.isAfter(moment())){
                vm.Item.active = false;
                vm.disableStatus = true;
                vm.toBePublished = publishDate;
            } else {
                vm.disableStatus = false;
            }
            return this.Item;
        }), function (newVal) {

            if(angular.isDefined(timer)){
                $timeout.cancel(timer);
            }

            if (typeof newVal.id == 'undefined' || !newVal.id){
                watcher();
                return;
            }

            timer = $timeout(function () {
                vm.save().then(function (item) {

                    for (var i in autoSaveHooks){
                        autoSaveHooks[i].call(this, item);
                    }
                });
            }, 5000);

        }, true);

        $scope.$on(
            "$destroy",
            function( event ) {
                $timeout.cancel( timer );
            }
        );

        $rootScope.$on('recipe.preview', function (e, preview) {
            if (preview){
                autoSaveHooks.push($scope.refreshIframe);
            } else {
                autoSaveHooks.splice(autoSaveHooks.indexOf($scope.refreshIframe), 1);
            }
        });

        vm.onSelectFromMediaLibrary = function (item) {
            MLS.assign(vm.thumbUploadOptions.params,item)
                .then(function (res) {
                    vm.Item.thumb = res;
                    Helpers.toast('Saved!!!');
                });
        };

    }
})();
