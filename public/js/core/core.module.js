(function() {
    'use strict';

    var app = angular.module('app.core', [
        /*
         * Angular modules
         */
        'ngAnimate', /*'ngRoute',*/ 'ngSanitize', 'ngCookies', 'ui.router',
        /*
         * Our reusable cross app code modules
         */
        'blocks.exception', 'blocks.logger', 'blocks.router',
        /*
         * 3rd Party modules
         */
        'pascalprecht.translate', 'angularMoment', 'angular-loading-bar', 'angular-jwt',
        'mgcrea.ngStrap', 'trNgGrid', 'trNgGrid', 'ui.tree', 'angularFileUpload',
        'formFor', 'formFor.bootstrapTemplates', 'nvd3ChartDirectives', 'ngCkeditor'
    ]);

    app.filter('num', function() {
        return function(input) {
            return parseInt(input, 10);
        };
    });

})();
