(function() {
    'use strict';

    angular
        .module('app.users')
        .controller('UsersFormController', UsersFormController);

    UsersFormController.$inject = ['users.model', 'logger', 'users.form', '$translate', '$stateParams'];

    function UsersFormController(Users, logger, FormService, $translate, $stateParams) {
        var vm = this;
        var user = null;
        vm.selectOptions = [{
            label: 'Yes',
            value: 'Y'
        }, {
            label: 'No',
            value: 'N'
        }];
        vm.formData = {};
        vm.submitFailed = submitFailed;
        vm.validationFailed = validationFailed;

        activate();

        function activate() {
            if ($stateParams.id) {
                vm.formData = Users.editUser($stateParams.id);
                vm.title = 'Users.Edit';
            } else {
                vm.formData = Users.createUser();
                vm.title = 'Users.Create';
            }
            vm.validationRules = FormService.getValidationRules();
            vm.submit = vm.validationRules.submit;
        }

        function validationFailed() {
            return logger.error("validaton faliled");
        }

        function submitFailed(error) {
            logger.error(error);
        }
    }
})();
