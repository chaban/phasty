(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('discount.model', DiscountModel);

    DiscountModel.$inject = ['restmod', 'logger', '$state', 'brands.model', 'categories.model', 'common'];

    function DiscountModel(restmod, logger, $state, brands, categories, common) {
        var discount = restmod.model('/admin/discounts');
        //var collection = discount.$collection();

        var service = {
            getAll: getAll,
            deleteDiscount: deleteDiscount,
            editDiscount: editDiscount,
            createDiscount: createDiscount
        };

        return service;

        function getAll() {
            return discount.$search().$then(function(_discounts) {
                return _discounts;
            }, function() {
                logger.error('Discount not found');
            });
        }

        function editDiscount(id) {
            return discount.$find(id).$then(function(_discount) {
                return _discount;
            }, function(reason) {
                logger.error('Discount not found');
                $state.go('discount.index');
            });
        }

        function createDiscount() {
            var _brands = brands.getAll().$then(function(_c) {
                return _c.$response.data.brands;
            }, function() {
                logger.error('Cannot retrive brands');
                $state.go('discount.index');
            });
            var _categories = categories.getAll().$then(function(_c) {
                return _c.$response.data.categories;
            }, function() {
                logger.error('Cannot retrive categories');
                $state.go('discount.index');
            });
            var deferred = common.$q.defer();
            var newDiscount = common.$timeout(function() {
                return discount.$build({
                    name: '',
                    active: 'Y',
                    startDate: '',
                    endDate: '',
                    sum: '',
                    brands: _brands,
                    brandIds: [],
                    categories: common.normalizeArray(_categories),
                    categoryIds: []
                });
            }, 2000);
            deferred.resolve(newDiscount);
            return deferred.promise;
        }

        function deleteDiscount(id) {
            var _discount = discount.$find(id);
            _discount.$destroy().$then(function() {
                logger.info('Discount destroyed');
            }, function() {
                logger.error('Something went wrong');
            });
        }
    }
})();
