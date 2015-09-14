(function() {
    'use strict';

    angular.module('app.data')
        .factory('delivery.form', DeliveryFormService);

    DeliveryFormService.$inject = ['common', 'logger'];

    function DeliveryFormService(common, logger) {
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
                    price: {
                        required: {
                            rule: true,
                            message: 'Price is required'
                        },
                        pattern: /^[0-9]+(\.[0-9]{1,2})?$/
                    },
                    freeFrom: {
                        required: {
                            rule: true,
                            message: 'Free from is required'
                        },
                        pattern: /^[0-9]+(\.[0-9]{1,2})?$/
                    },
                    position: {
                        required: {
                            rule: true,
                            message: 'Position is required'
                        },
                        pattern: /^\d+$/
                    }
                },
                submit: function(data) {
                    delete data.payments;
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
