(function() {
    'use strict';

    angular
        .module('app.layout')
        .controller('ShellController', ShellController);

    ShellController.$inject = ['$scope', 'config', 'common', 'logger', '$cookieStore',
        'routehelper', '$translate', '$window'
    ];

    function ShellController($scope, config, common, logger, $cookieStore, routehelper, $translate, $window) {

        var translations = config.translations;
        $scope.navRoutes = [];
        $scope.avatarDropdown = [];
        $scope.selected = null;
        $scope.toggleSidebar = toggleSidebar;
        $scope.getWidth = getWidth;
        $scope.changeLanguage = changeLanguage;

        activate();

        function activate() {
            $scope.selected = common.activeLang();
            $translate.use($scope.selected);
            getAvatarDropdown();
            getNavRoutes();
            logger.success(config.appTitle + ' loaded!', null);
        }

        function changeLanguage(lang) {
            $cookieStore.put('locale_lang', lang);
            $translate.use(lang);
            $scope.selected = lang;
        }

        function getNavRoutes() {
            var routes = routehelper.getRoutes();
            $scope.navRoutes = routes.filter(function(r) {
                return r.data && r.data.nav;
            }).sort(function(r1, r2) {
                return r1.data.nav - r2.data.nav;
            });
        }

        /**
         * Sidebar toggleSidebar & Cookie Control
         *
         */
        var mobileView = 992;

        function getWidth() {
            return $window.innerWidth;
        }

        $scope.$watch($scope.getWidth, function(newValue, oldValue) {
            if (newValue >= mobileView) {
                if (angular.isDefined($cookieStore.get('showSidebar'))) {

                    if ($cookieStore.get('showSidebar') === false) {
                        $scope.showSidebar = false;
                    } else {
                        $scope.showSidebar = true;
                    }
                } else {
                    $scope.showSidebar = true;
                }
            } else {
                $scope.showSidebar = false;
            }

        });

        function toggleSidebar() {
            $scope.showSidebar = !$scope.showSidebar;
            $cookieStore.put('showSidebar', $scope.showSidebar);
        }

        $window.onresize = function() {
            $scope.$apply();
        };

        function getAvatarDropdown() {
            $scope.avatarDropdown = [{
                "text": "<i class=\"fa fa-download\"></i>&nbsp;Another action",
                "href": "#anotherAction"
            }, {
                "text": "<i class=\"fa fa-globe\"></i>&nbsp;Display an alert",
                "click": "$alert(\"Holy guacamole!\")"
            }, {
                "text": "<i class=\"fa fa-external-link\"></i>&nbsp;External link",

                "href": "/auth/facebook",
                "target": "_self"
            }, {
                "divider": true
            }, {
                "text": "Separated link",
                "href": "#separatedLink"
            }];
        }
    }
})();
