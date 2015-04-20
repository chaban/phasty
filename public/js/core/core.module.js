(function() {
    'use strict';

    angular.module('app.core', [
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
        'pascalprecht.translate', 'angularMoment', 'angular-loading-bar',
        'mgcrea.ngStrap', 'trNgGrid', 'ui.tree', 'angularFileUpload',
        'formFor', 'formFor.bootstrapTemplates'
    ]);
})();
