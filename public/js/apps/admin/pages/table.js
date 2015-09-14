(function() {
    'use strict';

    angular
        .module('app.pages')
        .controller('PagesTableController', PagesTableController);

    PagesTableController.$inject = ['pages.model', 'logger', '$translate', 'modal.dialog'];

    function PagesTableController(Pages, logger, $translate, mkModalDialog) {
        /*jshint validthis: true */
        var vm = this;
        vm.fields = ['id', 'name', 'createdAt', 'updatedAt', 'active'];
        vm.deleteResource = deleteResource;
        vm.getAll = getAll;
        vm.showModal = showModal;

        activate();

        function activate() {
            $translate('Static_pages.Title').then(function(tr) {
                vm.title = tr;
            });
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete);
            });
            getAll();
        }

        function getAll() {
            vm.tableItems = Pages.getAll();
            logger.success('Pages loaded');
        }

        function showModal(gridItem) {
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete, gridItem.name);
            });
        }

        function deleteResource(gridItem) {
            vm.modal.show = false;
            Pages.deletePage(gridItem.id);
        }
    }
})();
