(function() {
    'use strict';

    angular
        .module('app.products')
        .controller('ProductsTableController', ProductsTableController);

    ProductsTableController.$inject = ['products.model', 'logger', '$translate', 'modal.dialog'];

    function ProductsTableController(Products, logger, $translate, mkModalDialog) {
        /*jshint validthis: true */
        var vm = this;
        vm.fields = ['id', 'name', 'price', 'category', 'brand', 'quantity', 'availability', 'rating', 'active'];
        vm.deleteResource = deleteResource;
        vm.getAll = getAll;
        vm.showModal = showModal;

        activate();

        function activate() {
            $translate('Products.Title').then(function(tr) {
                vm.title = tr;
            });
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete);
            });
            getAll();
        }

        function getAll() {
            vm.tableItems = Products.getAll();
            logger.success('Products loaded');
        }

        function showModal(gridItem) {
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete, gridItem.name);
            });
        }

        function deleteResource(gridItem) {
            vm.modal.show = false;
            Products.deleteProduct(gridItem.id);
        }
    }
})();
