(function() {
    'use strict';

    angular
        .module('app.widgets')
        .directive('mkWidgetTable', mkWidgetTable);

    /* @ngInject */
    function mkWidgetTable() {
        var directive = {
            scope: {
                'deleteResource': '=',
                'controllerName': '=',
                'items': '=',
                'totalItems': '=',
                'fields': '=',
                'pageItems': '='
            },
            templateUrl: '/js/widgets/widgetTable.html',
            replace: true,
            restrict: 'A',
            /*link: function(scope, element, attr) {
                console.log(attr);
            }*/
        };
        return directive;
    }
})();