(function() {
    'use strict';

    angular
        .module('app.orders')
        .run(appRun);

    appRun.$inject = ['routehelper'];
    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'orders': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'orders.index': {
                url: '/orders',
                templateUrl: '/js/apps/admin/orders/html/table.html',
                controller: 'OrdersTableController as vm',
                title: 'Orders.Title',
                parent: 'orders',
                data: {
                    nav: 8,
                    title: 'Orders.Url'
                }
            },
            'orders.create': {
                url: '/orders/create',
                templateUrl: '/js/apps/admin/orders/html/form.html',
                controller: 'OrdersFormController as vm',
                title: 'Orders.Create',
                parent: 'orders',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'orders.edit': {
                url: '/orders/:id/edit',
                templateUrl: '/js/apps/admin/orders/html/form.html',
                controller: 'OrdersFormController as vm',
                title: 'Orders.Edit',
                parent: 'orders',
                data: {
                    nav: 0,
                    title: ''
                }
            }
        };
    }
})();
