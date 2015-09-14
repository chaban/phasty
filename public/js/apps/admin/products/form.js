(function() {
    'use strict';

    angular
        .module('app.products')
        .controller('ProductsFormController', ProductsFormController);

    ProductsFormController.$inject = ['products.model', 'logger', 'products.form', '$translate', '$stateParams'];

    function ProductsFormController(Products, logger, FormService, $translate, $stateParams) {
        var vm = this;
        var product = null;
        vm.formData = {};
        vm.submitFailed = submitFailed;
        vm.validationFailed = validationFailed;
        vm.selectOptions = [{
            label: 'Yes',
            value: 'Y'
        }, {
            label: 'No',
            value: 'N'
        }];
        activate();

        function activate() {
            if ($stateParams.id) {
                vm.formData = Products.editProduct($stateParams.id);
                vm.title = 'Products.Edit';
            } else {
                Products.createProduct().then(function(data) {
                    vm.formData = data;
                });
                vm.title = 'Products.Create';
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
