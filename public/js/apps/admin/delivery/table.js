(function() {
    'use strict';

    angular
        .module('app.delivery')

    .controller('DeliveryTableController', DeliveryTableController);

    DeliveryTableController.$inject = ['delivery.model', 'logger', '$translate', 'modal.dialog'];

    function DeliveryTableController(Delivery, logger, $translate, mkModalDialog) {

        /*jshint validthis: true */
        var vm = this;
        vm.fields = ['id', 'name', 'price', 'freeFrom', 'position', 'active'];
        vm.deleteResource = deleteResource;
        vm.getAll = getAll;
        vm.showModal = showModal;
        activate();

        function activate() {
            $translate('Delivery.Title').then(function(tr) {
                vm.title = tr;
            });
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete);
            });
            getAll();
        }

        function getAll() {
            vm.tableItems = Delivery.getAll();
            logger.success('Delivery loaded');
        }

        function showModal(gridItem) {
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete, gridItem.name);
            });
        }

        function deleteResource(gridItem) {
            vm.modal.show = false;
            Delivery.deleteDelivery(gridItem.id);
        }
    }
})();
