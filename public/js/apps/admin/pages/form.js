(function() {
    'use strict';

    angular
        .module('app.pages')
        .controller('PagesFormController', PagesFormController);

    PagesFormController.$inject = ['pages.model', 'logger', 'pages.form', '$translate', '$stateParams'];

    function PagesFormController(Pages, logger, FormService, $translate, $stateParams) {
        var vm = this;
        var page = null;
        vm.formData = {};
        vm.submitFailed = submitFailed;
        vm.validationFailed = validationFailed;
        vm.editorOptions = {
            language: 'ru',
            uiColor: '#000000'
        };
        vm.optionsForRadioButton = [{
            label: 'Active',
            value: 'Y'
        }, {
            label: 'Not Active',
            value: 'N'
        }];

        activate();

        function activate() {
            if ($stateParams.id) {
                vm.formData = Pages.editPage($stateParams.id);
                vm.title = 'Static_pages.Edit';
            } else {
                vm.formData = Pages.createPage();
                vm.title = 'Static_pages.Create';
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
