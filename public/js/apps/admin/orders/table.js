(function() {
    'use strict';

    angular
        .module('app.orders')
        .controller('OrdersTableController', OrdersTableController);

    OrdersTableController.$inject = ['orders.model', 'logger', '$translate', 'modal.dialog'];

    function OrdersTableController(Orders, logger, $translate, mkModalDialog) {
        /*jshint validthis: true */
        var vm = this;
        vm.fields = ['id', 'userName', 'deliveryName', 'statusName', 'totalPrice', 'createdAt', 'updatedAt'];
        vm.deleteResource = deleteResource;
        vm.tableActions = tableActions;
        vm.showModal = showModal;

        activate();

        function activate() {
            $translate('Orders.Title').then(function(tr) {
                vm.title = tr;
            });
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete);
            });
        }

        function tableActions(currentPage, pageItems, filterBy, filterByFields, orderBy, orderByReverse) {
            vm.tableItems = Orders.byPage(currentPage, pageItems, filterBy, filterByFields, orderBy, orderByReverse);
            vm.tableItems.$then(function(_collection) {
                vm.totalItems = _collection.$metadata.totalItems;
                vm.pageItems = _collection.$metadata.limit;
                vm.currentPage = _collection.$metadata.pageNumber ? _collection.$metadata.pageNumber : 0;
            });
            logger.info('Orders loaded');
        }

        function showModal(gridItem) {
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete, gridItem.name);
            });
        }

        function deleteResource(gridItem) {
            vm.modal.show = false;
            Orders.deleteOrder(gridItem.id);
        }
    }
})();
