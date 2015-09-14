(function() {
    'use strict';

    angular.module('app.data')
        .factory('pages.form', PagesFormService);

    PagesFormService.$inject = ['common', 'logger'];

    function PagesFormService(common, logger) {
        var service = {
            getValidationRules: getValidationRules
        };

        return service;

        function getValidationRules() {
            return {
                rules: {
                    name: {
                        required: {
                            rule: true,
                            message: 'Name is required'
                        }
                    },
                    content: {
                        required: {
                            rule: true,
                            message: 'Content is required'
                        }
                    }
                },
                submit: function(data) {
                    var deferred = common.$q.defer();
                    common.$timeout(function() {
                        data.$save().$then(function() {
                                logger.success('Your form has been successfully saved');
                            },
                            function(reason) {
                                logger.error('form validation failed');
                                return deferred.reject(reason.$response.data.message);
                            });
                    }, 100);
                    return deferred.promise;
                }
            };
        }
    }
}());
