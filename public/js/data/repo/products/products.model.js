(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('products.model', ProductsModel);

    ProductsModel.$inject = ['restmod', 'logger', '$state', 'brands.model', 'categories.model', 'common'];

    function ProductsModel(restmod, logger, $state, brands, categories, common) {
        var products = restmod.model('/admin/products/');
        //var collection = products.$collection();

        var service = {
            getAll: getAll,
            deleteProduct: deleteProduct,
            editProduct: editProduct,
            createProduct: createProduct
        };

        return service;

        function getAll() {
            return products.$search().$then(function(_products) {
                return _products;
            }, function() {
                logger.error('Pages not found');
            });
        }

        function editProduct(id) {
            var page = products.$find(id).$then(function(_page) {
                return _page;
            }, function(reason) {
                logger.error('Product not found');
                $state.go('products.index');
            });
            return page;
        }

        function createProduct() {
            var _brands = brands.getAll().$then(function(_c) {
                return _c.$response.data.brands;
            }, function() {
                logger.error('Cannot retrive brands');
                $state.go('products.index');
            });
            var _categories = categories.getAll().$then(function(_c) {
                return _c.$response.data.categories;
            }, function() {
                logger.error('Cannot retrive categories');
                $state.go('products.index');
            });
            var deferred = common.$q.defer();
            var newProduct = common.$timeout(function() {
                return products.$build({
                    name: '',
                    active: 'Y',
                    availability: 'Y',
                    autoDecreaseQuantity: 'Y',
                    fullDescription: '',
                    price: '',
                    maxPrice: '',
                    quantity: '',
                    brands: _brands,
                    categories: common.normalizeArray(_categories),
                });
            }, 2000);
            deferred.resolve(newProduct);
            return deferred.promise;
        }

        function deleteProduct(id) {
            var item = products.$find(id);
            item.$destroy().$then(function() {
                logger.info('Product destroyed');
            }, function() {
                logger.error('Something went wrong');
            });
        }
    }
})();
