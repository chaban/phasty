(function() {
    'use strict';

    angular
        .module('app.discount')
        .controller('DiscountTableController', DiscountTableController);

    DiscountTableController.$inject = ['discount.model', 'logger', '$translate', 'modal.dialog'];

    function DiscountTableController(Discount, logger, $translate, mkModalDialog) {

        /*jshint validthis: true */
        var vm = this;
        vm.fields = ['id', 'name', 'sum', 'startDate', 'endDate', 'active'];
        vm.deleteResource = deleteResource;
        vm.getAll = getAll;
        vm.showModal = showModal;
        activate();

        function activate() {
            $translate('Discount.Title').then(function(tr) {
                vm.title = tr;
            });
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete);
            });
            getAll();
        }

        function getAll() {
            vm.tableItems = Discount.getAll();
            logger.success('Discount loaded');
        }

        function showModal(gridItem) {
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete, gridItem.name);
            });
        }

        function deleteResource(gridItem) {
            vm.modal.show = false;
            Discount.deleteDiscount(gridItem.id);
        }
    }
})();
