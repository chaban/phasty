(function() {
    'use strict';

    angular
        .module('app.core')
        .factory('common', common);

    common.$inject = ['$location', '$q', '$rootScope', '$timeout', 'logger', 'config', '$cookieStore'];

    /* @ngInject */
    function common($location, $q, $rootScope, $timeout, logger, config, $cookieStore) {
        var throttles = {};

        var service = {
            // common angular dependencies
            $broadcast: $broadcast,
            $q: $q,
            $timeout: $timeout,
            // generic
            createSearchThrottle: createSearchThrottle,
            debouncedThrottle: debouncedThrottle,
            isNumber: isNumber,
            logger: logger, // for accessibility
            replaceLocationUrlGuidWithId: replaceLocationUrlGuidWithId,
            textContains: textContains,
            langsForDropdown: langsForDropdown,
            activeLang: activeLang,
            categoriesForDropdown: categoriesForDropdown,
            normalizeArray: normalizeArray
        };

        return service;
        //////////////////////

        function $broadcast() {
            return $rootScope.$broadcast.apply($rootScope, arguments);
        }

        function createSearchThrottle(viewmodel, list, filteredList, filter, delay) {
            // After a delay, search a viewmodel's list using
            // a filter function, and return a filteredList.

            // custom delay or use default
            delay = +delay || 300;
            // if only vm and list parameters were passed, set others by naming convention
            if (!filteredList) {
                // assuming list is named sessions, filteredList is filteredSessions
                filteredList = 'filtered' + list[0].toUpperCase() + list.substr(1).toLowerCase(); // string
                // filter function is named sessionFilter
                filter = list + 'Filter'; // function in string form
            }

            // create the filtering function we will call from here
            var filterFn = function() {
                // translates to ...
                // vm.filteredSessions
                //      = vm.sessions.filter(function(item( { returns vm.sessionFilter (item) } );
                viewmodel[filteredList] = viewmodel[list].filter(function(item) {
                    return viewmodel[filter](item);
                });
            };

            return (function() {
                // Wrapped in outer IIFE so we can use closure
                // over filterInputTimeout which references the timeout
                var filterInputTimeout;

                // return what becomes the 'applyFilter' function in the controller
                return function(searchNow) {
                    if (filterInputTimeout) {
                        $timeout.cancel(filterInputTimeout);
                        filterInputTimeout = null;
                    }
                    if (searchNow || !delay) {
                        filterFn();
                    } else {
                        filterInputTimeout = $timeout(filterFn, delay);
                    }
                };
            })();
        }

        function debouncedThrottle(key, callback, delay, immediate) {
            // Perform some action (callback) after a delay.
            // Track the callback by key, so if the same callback
            // is issued again, restart the delay.

            var defaultDelay = 1000;
            delay = delay || defaultDelay;
            if (throttles[key]) {
                $timeout.cancel(throttles[key]);
                throttles[key] = undefined;
            }
            if (immediate) {
                callback();
            } else {
                throttles[key] = $timeout(callback, delay);
            }
        }

        function isNumber(val) {
            // negative or positive
            return (/^[-]?\d+$/).test(val);
        }

        function replaceLocationUrlGuidWithId(id) {
            // If the current Url is a Guid, then we replace
            // it with the passed in id. Otherwise, we exit.
            var currentPath = $location.path();
            var slashPos = currentPath.lastIndexOf('/', currentPath.length - 2);
            var currentParameter = currentPath.substring(slashPos - 1);

            if (isNumber(currentParameter)) {
                return;
            }

            var newPath = currentPath.substring(0, slashPos + 1) + id;
            $location.path(newPath);
        }

        function textContains(text, searchText) {
            return text && -1 !== text.toLowerCase().indexOf(searchText.toLowerCase());
        }

        function activeLang() {
            /*var url = $location.absUrl();
            var parts = url.split("/");
            var translations = config.translations;
            var lang;
            translations.forEach(function(tr) {
                var index = parts.indexOf(tr);
                if (index !== -1) {
                    lang = parts[index];
                }
            });*/
            if (angular.isDefined($cookieStore.get('locale_lang'))) {
                if ($cookieStore.get('locale_lang') === false) {
                    return config.defaultLang;
                } else {
                    return $cookieStore.get('locale_lang');
                }
            }
            //return lang;
        }

        function langsForDropdown() {
            return [{
                "value": "ru",
                "label": "<i class=\"flag-icon flag-icon-ru\"></i>"
            }, {
                "value": "en",
                "label": "<i class=\"flag-icon flag-icon-en\"></i>"
            }];
        }

        function categoriesForDropdown(all, temp, depth) {
            if (!temp) {
                temp = [];
            }
            if (!depth) {
                depth = '';
            }
            angular.forEach(all, function(value, key) {
                if (value.children && value.children.length) {
                    depth = depth + "--";
                    temp.push({
                        'value': value.id,
                        'label': value.title
                    });
                    categoriesForDropdown(value.children, temp, depth);
                    depth = '';
                } else {
                    temp.push({
                        'value': value.id,
                        'label': depth + value.title
                    });
                    depth = '';
                    delete value.children;
                }
            });
            return temp;
        }

        /**
         * from nested to simple array
         */

        function normalizeArray(all, temp, depth) {
            if (!temp) {
                temp = [];
            }
            if (!depth) {
                depth = '';
            }
            angular.forEach(all, function(value, key) {
                if (value.children && value.children.length) {
                    depth = depth + "--";
                    temp.push({
                        'id': value.id,
                        'title': value.title
                    });
                    normalizeArray(value.children, temp, depth);
                    depth = '';
                } else {
                    temp.push({
                        'id': value.id,
                        'title': depth + value.title
                    });
                    depth = '';
                    delete value.children;
                }
            });
            return temp;
        }
    }
})();
