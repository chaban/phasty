(function() {
    'use strict';

    angular
        .module('app.discount')
        .run(appRun);

    appRun.$inject = ['routehelper'];
    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'discount': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'discount.index': {
                url: '/discount',
                templateUrl: '/js/apps/admin/discount/html/table.html',
                controller: 'DiscountTableController as vm',

                title: 'Discount.Title',
                parent: 'discount',
                data: {
                    nav: 10,
                    title: 'Discount.Url'
                }
            },
            'discount.create': {
                url: '/discount/create',
                templateUrl: '/js/apps/admin/discount/html/form.html',
                controller: 'DiscountFormController as vm',
                title: 'Discount.Create',
                parent: 'discount',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'discount.edit': {
                url: '/discount/:id/edit',
                templateUrl: '/js/apps/admin/discount/html/form.html',
                controller: 'DiscountFormController as vm',
                title: 'Discount.Edit',
                parent: 'discount',
                data: {
                    nav: 0,
                    title: ''
                }
            }
        };
    }
})();
