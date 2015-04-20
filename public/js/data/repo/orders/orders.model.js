(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('orders.model', OrdersModel);

    OrdersModel.$inject = ['restmod', 'logger', '$state', 'common', 'delivery.model', 'discount.model', 'order-status.model', 'users.model'];

    function OrdersModel(restmod, logger, $state, common, deliveries, discounts, orderStatuses, users) {
        var orders = restmod.model('/admin/orders');
        //var collection = orders.$collection();

        var service = {
            byPage: byPage,
            deleteOrder: deleteOrder,
            editOrder: editOrder,
            createOrder: createOrder
        };

        return service;

        function byPage(currentPage, pageItems, filterBy, filterByFields, orderBy, orderByReverse) {
            var order = 'asc';
            if (!orderByReverse) {
                order = 'desc';
            }
            return orders.$search({
                page: currentPage,
                limit: pageItems,
                orderBy: orderBy,
                filterByFields: filterByFields,
                order: order
            });
        }

        function editOrder(id) {
            var page = orders.$find(id).$then(function(_page) {
                return _page;
            }, function(reason) {
                logger.error('Order not found');
                $state.go('orders.index');
            });
            return page;
        }

        function createOrder() {
            var _deliveries = deliveries.getAll().$then(function(_c) {
                return _c.$response.data.deliveries;
            }, function() {
                logger.error('Cannot retrive deliveries');
            });
            var _discounts = discounts.getAll().$then(function(_c) {
                return _c.$response.data.discounts;
            }, function() {
                logger.error('Cannot retrive discounts');
            });
            var _statuses = orderStatuses.getAll().$then(function(_c) {
                return _c.$response.data.orderStatuses;
            }, function() {
                logger.error('Cannot retrive order statuses');
            });
            var _users = users.byUser().$then(function(_c) {
                return _c.$response.data.users;
            }, function() {
                logger.error('Cannot retrive order statuses');
            });
            var deferred = common.$q.defer();
            var newOrder = common.$timeout(function() {
                return orders.$build({
                    deliveries: _deliveries,
                    discounts: _discounts,
                    statuses: _statuses,
                    users: _users,
                    userName: 'dummy',
                    email: '',
                    phone: '',
                    address: '',
                    adminComment: '',
                    userComment: ''
                });
            }, 1000);
            deferred.resolve(newOrder);
            return deferred.promise;
        }

        function deleteOrder(id) {
            var page = orders.$find(id);
            page.$destroy().$then(function() {
                logger.info('Order destroyed');
            }, function() {
                logger.error('Something went wrong');
            });
        }
    }
})();
