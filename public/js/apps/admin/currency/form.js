(function() {
    'use strict';

    angular
        .module('app.currency')
        .controller('CurrencyFormController', CurrencyFormController);

    CurrencyFormController.$inject = ['currency.model', 'logger', 'currency.form', '$translate', '$stateParams'];

    function CurrencyFormController(Currency, logger, FormService, $translate, $stateParams) {
        var vm = this;
        var page = null;
        vm.formData = {};
        vm.submitFailed = submitFailed;
        vm.validationFailed = validationFailed;
        activate();

        function activate() {
            if ($stateParams.id) {
                vm.formData = Currency.editCurrency($stateParams.id);
                vm.title = 'Currency.Edit';
            } else {
                vm.formData = Currency.createCurrency();
                vm.title = 'Currency.Create';
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
