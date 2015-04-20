(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('delivery.model', DeliveryModel);

    DeliveryModel.$inject = ['restmod', 'logger', '$state', 'payments.model', 'common'];

    function DeliveryModel(restmod, logger, $state, payments, common) {
        var delivery = restmod.model('/admin/deliveries').mix({
            $config: {
                name: 'delivery',
                plural: 'deliveries'
            }
        });
        //var collection = delivery.$collection();

        var service = {
            getAll: getAll,
            deleteDelivery: deleteDelivery,
            editDelivery: editDelivery,
            createDelivery: createDelivery
        };

        return service;

        function getAll() {
            return delivery.$search().$then(function(_deliveries) {
                return _deliveries;
            }, function() {
                logger.error('Delivery not found');
            });
        }

        function editDelivery(id) {
            return delivery.$find(id).$then(function(_delivery) {
                return _delivery;
            }, function(reason) {
                logger.error('Delivery not found');
                $state.go('delivery.index');
            });
        }

        function createDelivery() {
            var _payments = payments.getAll().$then(function(_c) {
                return _c.$response.data.payments;
            }, function() {
                logger.error('Cannot retrive payments');
                $state.go('delivery.index');
            });
            var deferred = common.$q.defer();
            var newDelivery = common.$timeout(function() {
                return delivery.$build({
                    name: '',
                    active: 'Y',
                    price: '',
                    freeFrom: '',
                    position: '',
                    payments: _payments,
                    paymentIds: []
                });
            }, 1000);
            deferred.resolve(newDelivery);
            return deferred.promise;
        }

        function deleteDelivery(id) {
            var _delivery = delivery.$find(id);
            _delivery.$destroy().$then(function() {
                logger.info('Delivery destroyed');
            }, function() {
                logger.error('Something went wrong');
            });
        }
    }
})();
