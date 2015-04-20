(function() {
    'use strict';

    angular
        .module('app.attributes')
        .controller('AttributeFormController', AttributeFormController);

    AttributeFormController.$inject = ['attributes.model', 'logger', 'attributes.form', '$translate', '$stateParams'];

    function AttributeFormController(Attributes, logger, FormService, $translate, $stateParams) {

        var vm = this;
        vm.formData = {};
        vm.submitFailed = submitFailed;
        vm.validationFailed = validationFailed;

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
    }
})();
