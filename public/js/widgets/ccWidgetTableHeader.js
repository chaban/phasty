(function() {
    'use strict';

    angular
        .module('app.widgets')
        .directive('ccWidgetTableHeader', ccWidgetTableHeader);

    /* @ngInject */
    function ccWidgetTableHeader() {
        //Usage:
        //<div data-cc-widget-header title="vm.map.title"></div>
        // Creates:
        // <div data-cc-widget-header=""
        //      title="Avengers Movie"
        //      allow-collapse="true" </div>
        var directive = {
            //            link: link,
            scope: {
                'ttitle': '='
            },
            templateUrl: '/js/widgets/widgetTableHeader.html',
            restrict: 'A'
        };
        return directive;

        //        function link(scope, element, attrs) {
        //            attrs.$set('class', 'widget-head');
        //        }
    }
})();