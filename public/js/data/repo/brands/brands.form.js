(function() {
    'use strict';

    angular.module('app.data')
        .factory('brands.form', BrandsFormService);

    BrandsFormService.$inject = ['common', 'logger'];

    function BrandsFormService(common, logger) {
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
