(function() {
    'use strict';

    angular
        .module('app.brands')

    .controller('BrandsFormController', BrandsFormController);

    BrandsFormController.$inject = ['brands.model', 'logger', 'brands.form', '$translate', '$stateParams'];

    function BrandsFormController(Brands, logger, FormService, $translate, $stateParams) {
        var vm = this;
        var page = null;
        vm.formData = {};
        vm.submitFailed = submitFailed;
        vm.validationFailed = validationFailed;

        activate();

        function activate() {
            if ($stateParams.id) {
                vm.formData = Brands.editBrand($stateParams.id);
                vm.title = 'Brands.Edit';
            } else {
                vm.formData = Brands.createBrand();
                vm.title = 'Brands.Create';
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
