(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('payments.model', PaymentssModel);

    PaymentssModel.$inject = ['restmod', 'logger', '$state', 'currency.model', 'common'];

    function PaymentssModel(restmod, logger, $state, currency, common) {
        var payments = restmod.model('/admin/payments');
        //var collection = payments.$collection();

        var service = {
            getAll: getAll,
            deletePayments: deletePayments,
            editPayments: editPayments,
            createPayments: createPayments
        };

        return service;

        function getAll() {
            return payments.$search().$then(function(_payments) {
                return _payments;
            }, function() {
                logger.error('Payments not found');
            });
        }

        function editPayments(id) {
            return payments.$find(id).$then(function(_payments) {
                return _payments;
            }, function(reason) {
                logger.error('Payments not found');
                $state.go('payments.index');
            });
        }

        function createPayments() {
            var _currencies = currency.getAll().$then(function(_c) {
                return _c.$response.data.currency;
            }, function() {
                logger.error('Cannot retrive currencies');
                $state.go('groups.index');
            });
            var deferred = common.$q.defer();
            var newPayment = common.$timeout(function() {
                return payments.$build({
                    name: '',
                    active: 'Y',
                    paymentSystem: '',
                    position: '',
                    currencies: _currencies,
                    currencyIds: []
                });
            }, 1000);
            deferred.resolve(newPayment);
            return deferred.promise;
        }

        function deletePayments(id) {
            var _payments = payments.$find(id);
            _payments.$destroy().$then(function() {
                logger.info('Payments destroyed');
            }, function() {
                logger.error('Something went wrong');
            });
        }
    }
})();
