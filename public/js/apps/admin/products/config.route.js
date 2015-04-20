(function() {
    'use strict';

    angular
        .module('app.products')
        .run(appRun);

    appRun.$inject = ['routehelper'];
    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'products': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'products.index': {
                url: '/products',
                templateUrl: '/js/apps/admin/products/html/table.html',
                controller: 'ProductsTableController as vm',
                title: 'Products.Title',
                parent: 'products',
                data: {
                    nav: 4,
                    title: 'Products.Url'
                }
            },
            'products.create': {
                url: '/products/create',
                templateUrl: '/js/apps/admin/products/html/form.html',
                controller: 'ProductsFormController as vm',
                title: 'Products.Create',
                parent: 'products',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'products.edit': {
                url: '/products/:id/edit',
                templateUrl: '/js/apps/admin/products/html/form.html',
                controller: 'ProductsFormController as vm',
                title: 'Products.Edit',
                parent: 'products',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'products.images': {
                url: '/products/:id/images',
                templateUrl: '/js/apps/admin/products/html/images.html',
                controller: 'ProductImagesController as vm',
                title: 'Products.Images',
                parent: 'products',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'products.attributes': {
                url: '/products/:id/attributes',
                templateUrl: '/js/apps/admin/products/html/attributes.html',
                controller: 'ProductAttributesController as vm',
                title: 'Products.Attributes',
                parent: 'products',
                data: {
                    nav: 0,
                    title: ''
                }
            }
        };
    }
})();
