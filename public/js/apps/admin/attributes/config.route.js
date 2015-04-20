(function() {
    'use strict';

    angular
        .module('app.attributes')
        .run(appRun);

    appRun.$inject = ['routehelper'];
    /* @ngInject */
    function appRun(routehelper) {
        routehelper.configureRoutes(getRoutes());
    }

    function getRoutes() {
        return {
            'attributes': {
                url: '',
                template: '<div ui-view></div>',
                abstract: true
            },
            'attributes.index': {

                url: '/attributes',
                templateUrl: '/js/apps/admin/attributes/html/table.html',
                controller: 'AttributeController as vm',
                title: 'Attributes.Title',
                parent: 'attributes',
                data: {
                    nav: 3,
                    title: 'Attributes.Url'
                }
            },
            'attributes.create': {
                url: '/attributes/create',
                templateUrl: '/js/apps/admin/attributes/html/form.html',
                controller: 'AttributeFormController as vm',
                title: 'Attributes.Create',
                parent: 'attributes',
                data: {
                    nav: 0,
                    title: ''
                }
            },
            'attributes.edit': {
                url: '/attributes/:id/edit',
                templateUrl: '/js/apps/admin/attributes/html/form.html',
                controller: 'AttributeFormController as vm',
                title: 'Attributes.Edit',
                parent: 'attributes',
                data: {
                    nav: 0,
                    title: ''
                }
            }
        };
    }
})();
