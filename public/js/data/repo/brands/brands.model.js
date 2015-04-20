(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('brands.model', BrandsModel);

    BrandsModel.$inject = ['restmod', 'logger', '$state'];

    function BrandsModel(restmod, logger, $state) {
        var brands = restmod.model('/admin/brands');
        //var collection = brands.$collection();

        var service = {
            getAll: getAll,
            deleteBrand: deleteBrand,
            editBrand: editBrand,
            createBrand: createBrand
        };

        return service;

        function getAll() {
            return brands.$search().$then(function(_brands) {
                return _brands;
            }, function() {
                logger.error('Attributes not found');
            });
        }

        function editBrand(id) {
            var brand = brands.$find(id).$then(function(_brand) {
                return _brand;
            }, function(reason) {
                logger.error('Brand not found');
                $state.go('brands.index');
            });
            return brand;
        }

        function createBrand() {
            return brands.$build({
                name: '',
                description: '',
                seoTitle: '',
                seoDescription: '',
                seoKeywords: ''
            });
        }

        function deleteBrand(id) {
            var brand = brands.$find(id);
            brand.$destroy().$then(function() {
                logger.info('Brand destroyed');
            }, function() {
                logger.error('Something went wrong');
            });
        }
    }
})();
