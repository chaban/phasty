(function() {
    'use strict';

    angular
        .module('app.brands')
        .controller('BrandsTableController', BrandsTableController);

    BrandsTableController.$inject = ['brands.model', 'logger', '$translate', 'modal.dialog'];

    function BrandsTableController(Brands, logger, $translate, mkModalDialog) {
        /*jshint validthis: true */
        var vm = this;
        vm.fields = ['id', 'name'];
        vm.deleteResource = deleteResource;
        vm.getAll = getAll;
        vm.showModal = showModal;

        activate();

        function activate() {
            $translate('Brands.Title').then(function(tr) {

                vm.title = tr;
            });
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete);
            });
            getAll();
        }

        function getAll() {
            vm.tableItems = Brands.getAll();
            logger.success('Brands loaded');
        }

        function showModal(gridItem) {
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete, gridItem.name);
            });
        }

        function deleteResource(gridItem) {
            vm.modal.show = false;
            Brands.deleteBrand(gridItem.id);
        }
    }
})();
