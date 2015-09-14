(function() {
    'use strict';

    angular.module('app.data')
        .factory('products.form', ProductsFormService);

    ProductsFormService.$inject = ['common', 'logger', '$state'];

    function ProductsFormService(common, logger, $state) {
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
                        pattern: /^\d+$/
                    },
                    maxPrice: {
                        required: {
                            rule: true,
                            message: 'max price is required'
                        },
                        pattern: /^\d+$/
                    },
                    quantity: {
                        required: {
                            rule: true,
                            message: 'quantity is required'
                        },
                        pattern: /^\d+$/
                    },
                    fullDescription: {
                        required: {
                            rule: true,
                            message: 'full description is required'
                        }
                    }
                },
                submit: function(data) {
                    delete data.categories;
                    delete data.brands;
                    var deferred = common.$q.defer();
                    common.$timeout(function() {
                        data.$save().$then(function() {
                                logger.success('Your form has been successfully saved');
                                $state.go('products.index');
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
