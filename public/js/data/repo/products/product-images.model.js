(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('product-images.model', ProductImagesModel);

    ProductImagesModel.$inject = ['restmod', 'logger', '$state', 'common'];

    function ProductImagesModel(restmod, logger, $state, common) {
        var images = restmod.model('/admin/productImages/');
        //var collection = products.$collection();

        var service = {
            deleteImage: deleteImage,
            editProductImages: editProductImages
        };

        return service;

        function editProductImages(id) {
            return images.$find(id).$then(function(_result) {
                return _result;
            }, function(reason) {
                logger.error('Images not found');
                $state.go('products.index');
            });
        }

        function deleteImage(id, name) {
            var image = images.$find(id).$then(function(_result) {
                return _result;
            }, function(reason) {
                logger.error('Images not found');
                $state.go('products.index');
            });

            common.$timeout(function() {
                delete image.images;
                image.imageName = name;
                image.$save();
            }, 400);
        }
    }
})();
