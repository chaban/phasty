(function() {
    'use strict';

    angular
        .module('app.attributes')
        .controller('AttributeFormController', AttributeFormController);

    AttributeFormController.$inject = ['attributes.model', 'logger', 'attributes.form', '$translate', '$stateParams', '$scope'];

    function AttributeFormController(Attributes, logger, FormService, $translate, $stateParams, $scope) {

        var vm = this;
        vm.formData = {};
        vm.submitFailed = submitFailed;
        vm.validationFailed = validationFailed;
        vm.usedAttributes = [];
        vm.disableName = true;
        $scope.$watch('vm.formData.categoryId', getUsedAttributes);
        $scope.$watch('vm.formData.name', isNameUsed);

        activate();

        function activate() {
            if ($stateParams.id) {
                vm.formData = Attributes.editAttribute($stateParams.id);
                vm.title = 'Attributes.Edit';
            } else {
                Attributes.createAttribute().then(function(data) {
                    vm.formData = data;
                });
                vm.title = 'Attributes.Create';
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

        function getUsedAttributes(categoryId) {
            if (categoryId) {
                vm.usedAttributes = Attributes.getUsedAttributes(categoryId);
                vm.disableName = false;
            }
        }

        function isNameUsed(name) {
            angular.forEach(vm.usedAttributes, function(value, key) {
                var usedName = value.name;
                if (name.length && (usedName.trim().toLowerCase().search(name.trim().toLowerCase()) > -1)) {
                    $scope.formController.setFieldError('name', 'this name already used');
                }
            });
        }
    }
})();
