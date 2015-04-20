(function() {
    'use strict';

    angular
        .module('app.payments')
        .run(appRun);

    appRun.$inject = ['routehelper'];
    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'payments': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'payments.index': {
                url: '/payments',
                templateUrl: '/js/apps/admin/payments/html/table.html',
                controller: 'PaymentsTableController as vm',

                title: 'Payments.Title',
                parent: 'payments',
                data: {
                    nav: 10,
                    title: 'Payments.Url'
                }
            },
            'payments.create': {
                url: '/payments/create',
                templateUrl: '/js/apps/admin/payments/html/form.html',
                controller: 'PaymentsFormController as vm',
                title: 'Payments.Create',
                parent: 'payments',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'payments.edit': {
                url: '/payments/:id/edit',
                templateUrl: '/js/apps/admin/payments/html/form.html',
                controller: 'PaymentsFormController as vm',
                title: 'Payments.Edit',
                parent: 'payments',
                data: {
                    nav: 0,
                    title: ''
                }
            }
        };
    }
})();
