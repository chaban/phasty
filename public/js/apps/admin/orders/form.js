(function() {
    'use strict';

    angular
        .module('app.orders')
        .controller('OrdersFormController', OrdersFormController);

    OrdersFormController.$inject = ['orders.model', 'logger', 'orders.form', '$translate', '$stateParams'];

    function OrdersFormController(Orders, logger, FormService, $translate, $stateParams) {
        var vm = this;
        var page = null;
        vm.formData = {};
        vm.submitFailed = submitFailed;
        vm.validationFailed = validationFailed;

        activate();

        function activate() {
            if ($stateParams.id) {
                vm.formData = Orders.editOrder($stateParams.id);
                vm.title = 'Orders.Edit';
            } else {
                Orders.createOrder().then(function(data) {
                    vm.formData = data;
                });
                vm.title = 'Orders.Create';
                console.log(vm.formData);
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
