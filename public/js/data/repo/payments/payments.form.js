(function() {
    'use strict';

    angular.module('app.data')
        .factory('payments.form', CurrencyFormService);

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
                        }
                    },
                    /*paymentSystem: {
                        required: {
                            rule: true,
                            message: 'Payment system is required'
                        },
                    },*/
                    position: {
                        required: {
                            rule: true,
                            message: 'Position is required'
                        },
                        pattern: /^\d+$/
                    }
                },
                submit: function(data) {
                    delete data.currencies;
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
