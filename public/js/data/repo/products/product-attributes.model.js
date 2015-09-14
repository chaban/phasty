(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('product-attributes.model', ProductAttributesModel);

    ProductAttributesModel.$inject = ['restmod', 'logger', '$state', 'common'];

    function ProductAttributesModel(restmod, logger, $state, common) {
        var Attributes = restmod.model('/admin/productAttributes/');
        //var collection = products.$collection();

        var service = {
            deleteImage: deleteImage,
            editProductAttributes: editProductAttributes
        };

        return service;

        function editProductAttributes(id) {
            return Attributes.$find(id).$then(function(_result) {
                angular.forEach(_result.attributes, function(value, key) {
                    if (value.type == 'int') {
                        value.value = parseInt(value.value);
                    }
                });
                return _result;
            }, function(reason) {
                logger.error('Attributes not found');
                //$state.go('products.index');
            });
        }

        function deleteImage(id, name) {
            var image = Attributes.$find(id).$then(function(_result) {
                return _result;
            }, function(reason) {
                logger.error('Attributes not found');
                //$state.go('products.index');
            });

            common.$timeout(function() {
                delete image.Attributes;
                image.imageName = name;
                image.$save();
            }, 400);
        }
    }
})();
