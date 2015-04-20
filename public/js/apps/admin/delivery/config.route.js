(function() {
    'use strict';

    angular
        .module('app.delivery')
        .run(appRun);

    appRun.$inject = ['routehelper'];
    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'delivery': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'delivery.index': {
                url: '/delivery',
                templateUrl: '/js/apps/admin/delivery/html/table.html',
                controller: 'DeliveryTableController as vm',

                title: 'Delivery.Title',
                parent: 'delivery',
                data: {
                    nav: 10,
                    title: 'Delivery.Url'
                }
            },
            'delivery.create': {
                url: '/delivery/create',
                templateUrl: '/js/apps/admin/delivery/html/form.html',
                controller: 'DeliveryFormController as vm',
                title: 'Delivery.Create',
                parent: 'delivery',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'delivery.edit': {
                url: '/delivery/:id/edit',
                templateUrl: '/js/apps/admin/delivery/html/form.html',
                controller: 'DeliveryFormController as vm',
                title: 'Delivery.Edit',
                parent: 'delivery',
                data: {
                    nav: 0,
                    title: ''
                }
            }
        };
    }
})();
