(function() {
    'use strict';

    angular
        .module('app.currency')
        .run(appRun);

    appRun.$inject = ['routehelper'];
    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'currency': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'currency.index': {
                url: '/currency',
                templateUrl: '/js/apps/admin/currency/html/table.html',
                controller: 'CurrencyTableController as vm',
                title: 'Currency.Title',
                parent: 'currency',
                data: {
                    nav: 10,
                    title: 'Currency.Url'
                }
            },
            'currency.create': {
                url: '/currency/create',
                templateUrl: '/js/apps/admin/currency/html/form.html',
                controller: 'CurrencyFormController as vm',
                title: 'Currency.Create',
                parent: 'currency',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'currency.edit': {
                url: '/currency/:id/edit',
                templateUrl: '/js/apps/admin/currency/html/form.html',
                controller: 'CurrencyFormController as vm',
                title: 'Currency.Edit',
                parent: 'currency',
                data: {
                    nav: 0,
                    title: ''
                }
            }
        };
    }
})();
