(function() {
    'use strict';

    angular
        .module('app.products')
        .controller('ProductImagesController', ProductImagesController);

    ProductImagesController.$inject = ['product-images.model', '$stateParams', '$state', 'logger', 'products.model', 'common', '$upload'];

    function ProductImagesController(Images, $stateParams, $state, logger, Products, common, $upload) {
        var vm = this;
        vm.hideAll = false;
        vm.uploadImage = uploadImage;
        vm.deleteImage = deleteImage;
        vm.refreshPage = refreshPage;
        vm.modalMessage = 'Are you sure?';

        activate();

        function activate() {
            if ($stateParams.id) {
                vm.product = Products.editProduct($stateParams.id);
                vm.data = Images.editProductImages($stateParams.id);
                vm.title = 'Products.Edit';
            } else {
                logger.error('There is no images for such product');
                $state.go('products.index');
            }
        }

        function uploadImage(files) {
            var id = vm.product.$pk;
            if (files && files.length) {
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    $upload.upload({
                        url: '/admin/productImages/',
                        /*headers: {
                            'Content-Type': file.type
                        },*/
                        method: 'POST',
                        fields: {
                            'id': id
                        },
                        file: file
                    }).progress(function(evt) {
                        var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                        console.log('progress: ' + progressPercentage + '% ' + evt.config.file.name);
                    }).success(function(data, status, headers, config) {
                        console.log('file ' + config.file.name + 'uploaded. Response: ' + data);
                    });
                }
                return refresh();
            }
        }

        function deleteImage(id, name) {
            if (window.confirm(vm.modalMessage)) {
                Images.deleteImage(id, name);
                return refresh();
            }
        }

        function refreshPage() {
            return refresh();
        }

        function refresh() {
            common.$timeout(function() {
                vm.data = Images.editProductImages($stateParams.id);
            }, 2000);
        }
    }
})();
