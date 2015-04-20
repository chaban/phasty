(function() {
    'use strict';

    angular
        .module('app.attributes')
        .controller('AttributeController', AttributeController);

    AttributeController.$inject = ['attributes.model', 'logger', '$translate', 'modal.dialog'];

    function AttributeController(Attributes, logger, $translate, mkModalDialog) {

        /*jshint validthis: true */
        var vm = this;
        vm.fields = ['id', 'name', 'filter', 'position', 'category'];
        vm.deleteResource = deleteResource;
        vm.getAll = getAll;
        vm.showModal = showModal;

        activate();

        function activate() {
            $translate('Attributes.Title').then(function(tr) {
                vm.title = tr;
            });
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete);
            });
            getAll();
        }

        function getAll() {
            vm.tableItems = Attributes.getAll();
            //console.log(vm.tableItems);
            logger.success('Attributes loaded');
        }

        function showModal(gridItem) {
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete, gridItem.name);
            });
        }

        function deleteResource(gridItem) {
            vm.modal.show = false;
            Attributes.deleteAttribute(gridItem.id);
        }
    }
})();
