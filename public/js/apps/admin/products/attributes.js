(function() {
    'use strict';

    angular
        .module('app.products')
        .controller('ProductAttributesController', ProductAttributesController);

    ProductAttributesController.$inject = ['product-attributes.model', 'products.model', '$stateParams', '$state', 'logger', 'common', '$sce'];

    function ProductAttributesController(Attributes, Products, $stateParams, $state, logger, common, $sce) {
        var vm = this;
        vm.updateAttributeValues = updateAttributeValues;
        vm.deleteAttribute = deleteAttribute;
        vm.refreshPage = refreshPage;
        vm.modalMessage = 'Are you sure?';

        activate();

        function activate() {
            if ($stateParams.id) {
                vm.data = Attributes.editProductAttributes($stateParams.id);
                vm.title = 'Products.Edit';
            } else {
                logger.error('There is no Attributes for such product');
                //$state.go('products.index');
            }
        }

        function deleteAttribute(id, name) {
            if (window.confirm(vm.modalMessage)) {
                Attributes.deleteAttribute(id, name);
                return refresh();
            }
        }

        function updateAttributeValues() {
            //delete vm.data.category;
            //delete vm.data.product;
            common.$timeout(function() {
                vm.data.$save().$then(function() {
                        return logger.success('Your form has been successfully saved');
                    },
                    function(reason) {
                        return logger.error('Form validation failed for reason ' + reason.$response.data.message);
                    });
            }, 100);
        }

        function refreshPage() {
            return refresh();
        }

        function refresh() {
            common.$timeout(function() {
                vm.data = Attributes.editProductAttributes($stateParams.id);
            }, 2000);
        }
    }
})();
