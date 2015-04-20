(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('order-status.model', OrderStatusModel);

    OrderStatusModel.$inject = ['restmod', 'logger', '$state'];

    function OrderStatusModel(restmod, logger, $state) {
        var OrderStatus = restmod.model('/admin/orderStatuses/').mix({
            $config: {
                name: 'orderStatus',
                plural: 'orderStatuses'
            }
        });
        //var collection = OrderStatus.$collection();

        var service = {
            getAll: getAll,
            deleteOrderStatus: deleteOrderStatus,
            editOrderStatus: editOrderStatus,
            createOrderStatus: createOrderStatus
        };

        return service;

        function getAll() {
            return OrderStatus.$search();
        }

        function editOrderStatus(id) {
            return OrderStatus.$find(id).$then(function(_OrderStatus) {
                return _OrderStatus;
            }, function(reason) {
                logger.error('OrderStatus not found');
                $state.go('OrderStatus.index');
            });
        }

        function createOrderStatus() {
            return OrderStatus.$build({
                name: '',
                rate: '',
            });
        }

        function deleteOrderStatus(id) {
            var _OrderStatus = OrderStatus.$find(id);
            _OrderStatus.$destroy().$then(function() {
                logger.info('OrderStatus destroyed');
            }, function() {
                logger.error('Something went wrong');
            });
        }
    }
})();
