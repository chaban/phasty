(function() {
    'use strict';

    angular.module('app.data')
        .factory('currency.form', CurrencyFormService);

    CurrencyFormService.$inject = ['common', 'logger'];

    function CurrencyFormService(common, logger) {
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
                        },
                        minlength: 3,
                        maxlength: 3
                    },
                    rate: {
                        required: {
                            rule: true,
                            message: 'Rate is required'
                        },
                        pattern: /^[0-9]+[\.]?[0-9]+$/
                    }
                },
                submit: function(data) {
                    var deferred = common.$q.defer();
                    common.$timeout(function() {
                        data.$save().$then(function() {
                                logger.success('Your form has been successfully saved');
                            },
                            function(reason) {
                                return deferred.reject(reason.$response.data.message);
                            });
                    }, 100);
                    return deferred.promise;
                }
            };
        }
    }
}());
