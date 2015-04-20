(function() {
    'use strict';

    angular.module('app.data')
        .factory('discount.form', DiscountFormService);

    DiscountFormService.$inject = ['common', 'logger'];

    function DiscountFormService(common, logger) {
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
                    sum: {
                        required: {
                            rule: true,
                            message: 'Summ is required'
                        }
                    },
                    startDate: {
                        required: {
                            rule: true,
                            message: 'Start date is required'
                        }
                    },
                    endDate: {
                        required: {
                            rule: true,
                            message: 'End date is required'
                        }
                    }
                },
                submit: function(data) {
                    var deferred = common.$q.defer();
                    common.$timeout(function() {
                        data.$save().$then(function() {
                                logger.success('Your form has been successfully saved');
                            },
                            function() {
                                return deferred.reject('Form validation failed');
                            });
                    }, 100);
                    delete data.brands;
                    delete data.categories;
                    return deferred.promise;
                }
            };
        }
    }
}());
