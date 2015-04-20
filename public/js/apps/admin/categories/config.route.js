(function() {
    'use strict';

    angular
        .module('app.categories')
        .run(appRun);

    appRun.$inject = ['routehelper'];
    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'categories': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'categories.index': {
                url: '/categories',
                templateUrl: '/js/apps/admin/categories/html/categories.html',
                controller: 'CategoriesController as vm',
                title: 'Categories.Title',
                parent: 'categories',
                data: {
                    nav: 1,
                    title: 'Categories.Url'
                }
            }
        };
    }
})();
