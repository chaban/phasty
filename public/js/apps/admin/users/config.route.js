(function() {
    'use strict';

    angular
        .module('app.users')
        .run(appRun);

    appRun.$inject = ['routehelper'];
    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'users': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'users.index': {
                url: '/users',
                templateUrl: '/js/apps/admin/users/html/table.html',
                controller: 'UsersTableController as vm',
                title: 'Users.Title',
                parent: 'users',
                data: {
                    nav: 11,
                    title: 'Users.Url'

                }
            },
            'users.create': {
                url: '/users/create',
                templateUrl: '/js/apps/admin/users/html/form.html',
                controller: 'UsersFormController as vm',
                title: 'Users.Create',
                parent: 'users',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'users.edit': {
                url: '/users/:id/edit',
                templateUrl: '/js/apps/admin/users/html/form.html',
                controller: 'UsersFormController as vm',
                title: 'Users.Edit',
                parent: 'users',
                data: {
                    nav: 0,
                    title: ''
                }
            }
        };
    }
})();
