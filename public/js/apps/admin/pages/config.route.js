(function() {
    'use strict';

    angular
        .module('app.pages')
        .run(appRun);

    appRun.$inject = ['routehelper'];
    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'pages': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'pages.index': {
                url: '/pages',
                templateUrl: '/js/apps/admin/pages/html/table.html',
                controller: 'PagesTableController as vm',
                title: 'Static_pages.Title',
                parent: 'pages',
                data: {
                    nav: 100,
                    title: 'Static_pages.Url'
                }
            },
            'pages.create': {
                url: '/pages/create',
                templateUrl: '/js/apps/admin/pages/html/form.html',
                controller: 'PagesFormController as vm',
                title: 'Static_pages.Create',
                parent: 'pages',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'pages.edit': {
                url: '/pages/:id/edit',
                templateUrl: '/js/apps/admin/pages/html/form.html',
                controller: 'PagesFormController as vm',
                title: 'Static_pages.Edit',
                parent: 'pages',
                data: {
                    nav: 0,
                    title: ''
                }
            }
        };
    }
})();
