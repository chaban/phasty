(function() {
    'use strict';

    angular
        .module('app.index')
        .run(appRun);

    appRun.$inject = ['routehelper'];

    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'index': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'index.dashboard': {
                url: '/index',
                parent: 'index',
                templateUrl: '/js/apps/admin/index/index.html',
                controller: 'IndexController as vm',
                title: 'Dashboard.Title',
                data: {
                    nav: 1,
                    title: 'Dashboard.Url'
                }
            }
        };
    }
})();
