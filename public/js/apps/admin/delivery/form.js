(function() {
    'use strict';

    angular
        .module('app.delivery')
        .controller('DeliveryFormController', DeliveryFormController);

    DeliveryFormController.$inject = ['delivery.model', 'logger', 'delivery.form', '$translate', '$stateParams'];

    function DeliveryFormController(Delivery, logger, FormService, $translate, $stateParams) {
        var vm = this;
        vm.formData = {};
        vm.submitFailed = submitFailed;
        vm.validationFailed = validationFailed;
        activate();

        function activate() {
            if ($stateParams.id) {
                vm.formData = Delivery.editDelivery($stateParams.id);
                vm.title = 'Delivery.Edit';
            } else {
                Delivery.createDelivery().then(function(data) {
                    vm.formData = data;
                });
                vm.title = 'Delivery.Create';
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
