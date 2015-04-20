(function() {
    'use strict';

    angular.module('app.data')
        .factory('users.form', PagesFormService);

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
                    email: {
                        required: {
                            rule: true,
                            message: 'Email is required'
                        },
                        pattern: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    },
                    password: {
                        required: {
                            rule: true,
                            message: 'Password is required'
                        },
                        minlength: 6,
                        maxlength: 100
                    },
                    address: {
                        required: {
                            rule: true,
                            message: 'Address is required'
                        },
                        minlength: 6
                    },
                    phone: {
                        required: {
                            rule: true,
                            message: 'Password is required'
                        },
                        pattern: /^[\d\+ -]+$/
                    },
                    confirmed: {
                        required: {
                            rule: true,
                            message: 'Confirmed is required'
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
                    return deferred.promise;
                }
            };
        }
    }
}());
