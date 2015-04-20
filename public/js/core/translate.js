(function() {
    'use strict';

    var core = angular.module('app.core');

    core.config(configure);

    configure.$inject = ['$translateProvider'];

    function configure($translateProvider) {
        $translateProvider.useStaticFilesLoader({
            prefix: '/js/languages/',
            suffix: '.json'
        });
        //$translateProvider.determinePreferredLanguage();
        $translateProvider.preferredLanguage('ru');
        //$translateProvider.useLocalStorage();
        $translateProvider.fallbackLanguage('ru');
    }
})();