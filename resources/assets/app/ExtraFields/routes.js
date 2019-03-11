(function() {
    'use strict';

    angular.module('mcms.recipes.extraFields')
        .config(config);

    config.$inject = ['$routeProvider','RECIPES_CONFIG'];

    function config($routeProvider,Config) {

        $routeProvider
            .when('/recipes/extraFields', {
                templateUrl:  Config.templatesDir + 'ExtraFields/index.html',
                controller: 'ExtraFieldHomeController',
                controllerAs: 'VM',
                reloadOnSearch : true,
                resolve: {
                    init : ["AuthService", '$q', function (ACL, $q) {
                        return (!ACL.inGates('cms.extraFields.menu')) ? $q.reject(403) : $q.resolve();
                    }]
                },
                name: 'recipes-extra-fields-home'
            });
    }

})();
