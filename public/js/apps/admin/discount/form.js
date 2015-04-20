(function() {
    'use strict';

    angular
        .module('app.discount')
        .controller('DiscountFormController', DiscountFormController);

    DiscountFormController.$inject = ['discount.model', 'logger', 'discount.form', '$translate', '$stateParams'];

    function DiscountFormController(Discount, logger, FormService, $translate, $stateParams) {
        var vm = this;
        vm.formData = {};
        vm.submitFailed = submitFailed;
        vm.validationFailed = validationFailed;
        activate();

        function activate() {
            if ($stateParams.id) {
                vm.formData = Discount.editDiscount($stateParams.id);
                vm.title = 'Discount.Edit';
            } else {
                Discount.createDiscount().then(function(data) {
                    vm.formData = data;
                });
                vm.title = 'Discount.Create';
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
