(function() {
    'use strict';

    angular
        .module('app.widgets')
        .directive('mkWidgetLangs', mkWidgetLangs);

    /* @ngInject */
    function mkWidgetLangs() {
        var directive = {
            scope: {
                'translations': '=',
                'selected': '=',
            },
            templateUrl: '/js/widgets/widgetLangs.html',
            replace: true,
            restrict: 'A',
            /*link: function(scope, element, attr) {
                console.log(attr);
            }*/
        };
        return directive;
    }
})();