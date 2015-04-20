(function() {
    'use strict';

    angular
        .module('blocks.router')
        .provider('routehelperConfig', routehelperConfig)
        .factory('routehelper', routehelper);

    routehelper.$inject = ['$rootScope', '$state', 'logger', 'routehelperConfig', '$translate', 'common'];

    // Must configure via the routehelperConfigProvider
    function routehelperConfig() {
        /* jshint validthis:true */
        this.config = {
            // These are the properties we need to set
            // $routeProvider: undefined
            // docTitle: ''
            // resolveAlways: {ready: function(){ } }
        };

        this.$get = function() {
            return {
                config: this.config
            };
        };
    }

    function routehelper($rootScope, $state, logger, routehelperConfig, $translate, common) {
        var handlingRouteChangeError = false;
        var routeCounts = {
            errors: 0,
            changes: 0
        };
        var routes = [];
        var $urlRouterProvider = routehelperConfig.config.$urlRouterProvider;
        var $stateProvider = routehelperConfig.config.$stateProvider;

        var service = {
            configureRoutes: configureRoutes,
            getRoutes: getRoutes,
            routeCounts: routeCounts
        };

        init();

        function init() {
            handleRoutingErrors();
            updateDocTitle();
        }

        return service;
        ///////////////

        function configureRoutes(routes) {
            for (var name in routes) {
                if (!$state.get(name)) {
                    $stateProvider.state(name, routes[name]);
                }
            }
            $urlRouterProvider.otherwise("index");
        }

        function handleRoutingErrors() {
            // Route cancellation:
            // On routing error, go to the dashboard.
            // Provide an exit clause if it tries to do it twice.
            $rootScope.$on('$stateNotFound',
                function(event, toState, toParams, fromState, fromParams, error) {
                    /*if (handlingRouteChangeError) {
                        return;
                    }*/
                    routeCounts.errors++;
                    handlingRouteChangeError = true;
                    var destination = (toState && (toState.title || toState.name)) || 'unknown target';
                    var msg = 'Error routing to ' + destination + '. ' + (error || '');
                    logger.warning(msg, [toState]);
                    console.log(toState, toParams, fromState, fromParams, error);
                    $state.go('index');
                }
            );
        }

        function getRoutes() {
            angular.forEach($state.get(), function(route) {
                if (route.data && route.data.nav !== 0) {
                    var isForSidebar = !!route.data.title;
                    if (isForSidebar) {
                        routes.push(route);
                    }
                }
            });
            return routes;
        }

        function updateDocTitle() {
            $rootScope.$on('$stateChangeSuccess',
                function(event, toState, toParams, fromState, fromParams) {
                    routeCounts.changes++;
                    handlingRouteChangeError = false;
                    var title = '';
                    var globalTitle = '';
                    $translate([routehelperConfig.config.docTitle, toState.title]).then(function(trs) {
                        $rootScope.currentTitle = trs[Object.keys(trs)[1]];
                        $rootScope.title = trs[Object.keys(trs)[0]] + '-' + (trs[Object.keys(trs)[1]] || '');
                    });
                }
            );
        }
    }
})();
