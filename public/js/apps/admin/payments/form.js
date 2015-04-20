(function() {
    'use strict';

    angular
        .module('app.payments')
        .controller('PaymentsFormController', PaymentsFormController);

    PaymentsFormController.$inject = ['payments.model', 'logger', 'payments.form', '$translate', '$stateParams'];

    function PaymentsFormController(Payments, logger, FormService, $translate, $stateParams) {
        var vm = this;
        vm.formData = {};
        vm.submitFailed = submitFailed;
        vm.validationFailed = validationFailed;
        activate();

        function activate() {
            if ($stateParams.id) {
                vm.formData = Payments.editPayments($stateParams.id);
                vm.title = 'Payments.Edit';
            } else {
                Payments.createPayments().then(function(data) {
                    vm.formData = data;
                    console.log(vm.formData);
                });
                vm.title = 'Payments.Create';
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
