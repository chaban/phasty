(function() {
    'use strict';

    angular
        .module('app.brands')
        .run(appRun);

    appRun.$inject = ['routehelper'];
    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'brands': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'brands.index': {
                url: '/brands',
                templateUrl: '/js/apps/admin/brands/html/table.html',
                controller: 'BrandsTableController as vm',
                title: 'Brands.Title',
                parent: 'brands',
                data: {
                    nav: 10,
                    title: 'Brands.Url'
                }
            },
            'brands.create': {
                url: '/brands/create',
                templateUrl: '/js/apps/admin/brands/html/form.html',
                controller: 'BrandsFormController as vm',

                title: 'Brands.Create',
                parent: 'brands',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'brands.edit': {
                url: '/brands/:id/edit',

                templateUrl: '/js/apps/admin/brands/html/form.html',
                controller: 'BrandsFormController as vm',
                title: 'Brands.Edit',
                parent: 'brands',
                data: {
                    nav: 0,
                    title: ''
                }
            }
        };
    }
})();
