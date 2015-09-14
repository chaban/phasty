(function() {
    'use strict';

    var core = angular.module('app.core');

    core.config(toastrConfig);

    toastrConfig.$inject = ['toastr'];

    function toastrConfig(toastr) {
        toastr.options.timeOut = 5000;
        toastr.options.positionClass = 'toast-bottom-left';
    }

    var config = {
        translations: ['ru', 'en'],
        defaultLang: 'en',
        appErrorPrefix: '[Phasty Error] ', //Configure the exceptionHandler decorator
        appTitle: 'Phasty e-commerce with Phalcon and Angularjs',
        version: '0.0.1'
    };

    core.value('config', config);

    core.config(configure);

    configure.$inject = ['$logProvider', 'routehelperConfigProvider', 'exceptionHandlerProvider',
        '$stateProvider', '$urlRouterProvider'
    ];

    function configure($logProvider, routehelperConfigProvider, exceptionHandlerProvider, $stateProvider, $urlRouterProvider) {
        // turn debugging off/on (no info or warn)
        if ($logProvider.debugEnabled) {
            $logProvider.debugEnabled(true);
        }

        // Configure the common route provider
        routehelperConfigProvider.config.$urlRouterProvider = $urlRouterProvider;
        routehelperConfigProvider.config.$stateProvider = $stateProvider;
        routehelperConfigProvider.config.docTitle = 'Admin_area';
        var resolveAlways = {
            //ready: function(dataservice) {
            //       return dataservice.ready();
            // }
            ready: ['dataservice', function(dataservice) {
                return dataservice.ready();
            }]
        };
        routehelperConfigProvider.config.resolveAlways = resolveAlways;

        // Configure the common exception handler
        exceptionHandlerProvider.configure(config.appErrorPrefix);
    }
})();
