(function() {
    'use strict';

    var core = angular.module('app.core');

    core.config(configure);

    configure.$inject = ['$httpProvider', 'jwtInterceptorProvider', '$injector'];

    function configure($httpProvider, jwtInterceptorProvider, $injector) {
        $httpProvider.interceptors.push(['$q', '$location', '$injector', function($q, $location, $injector) {
            var logger = $injector.get('logger');
            return {
                response: function(response) {
                    if (response.status === 401 || response.status === 403) {

                    }
                    return response || $q.when(response);
                },
                responseError: function(rejection) {
                    if (rejection.status === 401 || rejection.status === 403) {
                        logger.error('You have not access to this area');
                        $location.path('/index');
                    }
                    return $q.reject(rejection);
                }
            };
        }]);
        // Please note we're annotating the function so that the $injector works when the file is minified
        jwtInterceptorProvider.tokenGetter = function() {
            return localStorage.getItem('auth-token');
        };

        $httpProvider.interceptors.push('jwtInterceptor');
    }
})();
