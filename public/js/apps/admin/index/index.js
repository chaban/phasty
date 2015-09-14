(function() {
    'use strict';
    angular.module('app.index').controller('IndexController', IndexController);
    IndexController.$inject = ['index.model', 'logger', '$translate'];

    function IndexController(dataservice, logger, $translate) {
        /*jshint validthis: true */
        var vm = this;
        vm.getDashboardData = getDashboardData;
        vm.data = {};
        //vm.xFunction = xFunction;
        //vm.yFunction = yFunction;
        vm.xAxisTickFormatFunction = xAxisTickFormatFunction;
        vm.yAxisTickFormatFunction = yAxisTickFormatFunction;
        vm.toolTipContentFunction = toolTipContentFunction;

        activate();

        function activate() {
            $translate('Dashboard.Title').then(function(tr) {
                vm.title = tr;
            });
            getDashboardData();
        }

        function getDashboardData() {
            vm.data = dataservice.getDashboardData();
            return logger.info('Activated Dashboard View');
        }

        function xAxisTickFormatFunction() {
            return function(d) {
                //return new Date(d);
                return moment(d).format('LL');
            };
        }

        function yAxisTickFormatFunction() {

        }

        function toolTipContentFunction() {
            return function(key, x, y, e, graph) {
                return '<p>order on summ ' + y + ' </p><p> at ' + x + '</p>';
            };
        }
    }
})();
