(function() {
    'use strict';

    angular.module('app.data')
        .factory('attributes.form', AttributesFormService);

    AttributesFormService.$inject = ['common', 'logger'];

    function AttributesFormService(common, logger) {
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
                    position: {
                        required: {
                            rule: true,
                            message: 'Position is required'
                        },
                        pattern: /^\d+$/
                    },
                    filter: {
                        required: {
                            rule: true,
                            message: 'Filter is required'
                        }
                    }
                },
                submit: function(data) {
                    delete data.groups;
                    var deferred = common.$q.defer();
                    common.$timeout(function() {
                        data.$save().$then(function() {
                                logger.success('Your form has been successfully saved');
                            },
                            function() {
                                return deferred.reject('Form validation failed');
                            });
                    }, 100);
                    return deferred.promise;
                }
            };
        }
    }
}());
