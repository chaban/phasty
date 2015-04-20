(function() {
    'use strict';

    angular
        .module('app.users')
        .controller('UsersTableController', UsersTableController);

    UsersTableController.$inject = ['users.model', 'logger', '$translate', 'modal.dialog'];

    function UsersTableController(Users, logger, $translate, mkModalDialog) {
        /*jshint validthis: true */
        var vm = this;
        vm.fields = ['id', 'name', 'email', 'mustChangePassword', 'banned', 'confirmed'];
        vm.deleteResource = deleteResource;
        vm.tableActions = tableActions;
        vm.showModal = showModal;

        activate();

        function activate() {
            $translate('Users.Title').then(function(tr) {
                vm.title = tr;
            });
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete);
            });
        }

        function tableActions(currentUser, pageItems, filterBy, filterByFields, orderBy, orderByReverse) {
            vm.tableItems = Users.byUser(currentUser, pageItems, filterBy, filterByFields, orderBy, orderByReverse);
            vm.tableItems.$then(function(_collection) {
                vm.totalItems = _collection.$metadata.totalItems;
                vm.pageItems = _collection.$metadata.limit;
                vm.currentPage = _collection.$metadata.pageNumber ? _collection.$metadata.pageNumber : 0;
            });
            logger.info('Users loaded');
        }

        function showModal(gridItem) {
            $translate(['Confirm_Delete', 'Delete']).then(function(tr) {
                vm.modal = mkModalDialog.deleteDialog(tr.Confirm_Delete, tr.Delete, gridItem.name);
            });
        }

        function deleteResource(gridItem) {
            vm.modal.show = false;
            Users.deleteUser(gridItem.id);
        }
    }
})();
