(function() {
    'use strict';

    angular
        .module('app.payments')

    .controller('PaymentsTableController', PaymentsTableController);

    PaymentsTableController.$inject = ['payments.model', 'logger', '$translate', 'modal.dialog'];

    function PaymentsTableController(Payments, logger, $translate, mkModalDialog) {

        /*jshint validthis: true */
        var vm = this;
        vm.fields = ['id', 'name', 'paymentSystem', 'position'];
        vm.deleteResource = deleteResource;
        vm.getAll = getAll;
        vm.showModal = showModal;
        activate();

        function activate() {
            $translate('Payments.Title').then(function(tr) {
                vm.title = tr;
            });
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete);
            });
            getAll();
        }

        function getAll() {
            vm.tableItems = Payments.getAll();
            logger.success('Payments loaded');
        }

        function showModal(gridItem) {
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete, gridItem.name);
            });
        }

        function deleteResource(gridItem) {
            vm.modal.show = false;
            Payments.deletePayments(gridItem.id);
        }
    }
})();
