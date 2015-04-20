(function() {
    'use strict';

    angular
        .module('app.products')
        .controller('ProductsTableController', ProductsTableController);

    ProductsTableController.$inject = ['products.model', 'logger', '$translate', 'modal.dialog'];

    function ProductsTableController(Products, logger, $translate, mkModalDialog) {
        /*jshint validthis: true */
        var vm = this;
        vm.fields = ['id', 'name', 'price', 'rating', 'viewsCount', 'addedToCartCount', 'quantity', 'availability', 'active'];
        vm.deleteResource = deleteResource;
        vm.tableActions = tableActions;
        vm.showModal = showModal;

        activate();

        function activate() {
            $translate('Products.Title').then(function(tr) {
                vm.title = tr;
            });
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete);
            });
        }

        function tableActions(currentPage, pageItems, filterBy, filterByFields, orderBy, orderByReverse) {
            vm.tableItems = Products.byProduct(currentPage, pageItems, filterBy, filterByFields, orderBy, orderByReverse);
            vm.tableItems.$then(function(_collection) {
                vm.totalItems = _collection.$metadata.totalItems;
                vm.pageItems = _collection.$metadata.limit;
                vm.currentPage = _collection.$metadata.pageNumber ? _collection.$metadata.pageNumber : 0;
            });
            logger.info('Products loaded');
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
