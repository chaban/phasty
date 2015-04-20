(function() {
    'use strict';

    angular.module('app.data')
        .factory('orders.form', OrdersFormService);

    OrdersFormService.$inject = ['common', 'logger', '$state'];

    function OrdersFormService(common, logger, $state) {
        var service = {
            getValidationRules: getValidationRules
        };

        return service;

        function getValidationRules() {
            return {
                rules: {
                    email: {
                        required: {
                            rule: true,
                            message: 'Email is required'
                        },
                        pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    },
                    phone: {
                        required: {
                            rule: true,
                            message: 'Phone is required'
                        }
                    },
                    address: {
                        required: {
                            rule: true,
                            message: 'Address is required'
                        }
                    },
                },
                submit: function(data) {
                    delete data.users;
                    delete data.deliveries;
                    delete data.statuses;
                    delete data.discounts;
                    var deferred = common.$q.defer();
                    common.$timeout(function() {
                        data.$save().$then(function() {
                                logger.success('Your form has been successfully saved');
                                $state.go('orders.index');
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
