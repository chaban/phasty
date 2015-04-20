(function() {
    'use strict';

    angular
        .module('app.currency')
        .controller('CurrencyTableController', CurrencyTableController);

    CurrencyTableController.$inject = ['currency.model', 'logger', '$translate', 'modal.dialog'];

    function CurrencyTableController(Currency, logger, $translate, mkModalDialog) {
        /*jshint validthis: true */
        var vm = this;
        vm.fields = ['id', 'name', 'rate', 'createdAt', 'updatedAt'];
        vm.tableItems = [];
        vm.deleteResource = deleteResource;
        vm.getAll = getAll;
        vm.showModal = showModal;
        activate();

        function activate() {
            $translate('Currency.Title').then(function(tr) {
                vm.title = tr;
            });
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete);
            });
            getAll();
        }

        function getAll() {
            vm.tableItems = Currency.getAll();
            vm.tableItems.$then(function(_currency) {
                return _currency;
            }, function() {
                logger.error('Currency not found');
            });
            logger.success('Currency loaded');
        }

        function showModal(gridItem) {
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete, gridItem.name);
            });
        }

        function deleteResource(gridItem) {
            vm.modal.show = false;
            Currency.deleteCurrency(gridItem.id);
        }
    }
})();
