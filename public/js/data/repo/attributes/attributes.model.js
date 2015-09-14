(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('attributes.model', AttributesModel);

    AttributesModel.$inject = ['restmod', 'logger', 'categories.model', 'common', '$state'];

    function AttributesModel(restmod, logger, categories, common, $state) {

        var attributes = restmod.model('/admin/attributes');
        //var collection = Attributes.$collection();

        var service = {
            getAll: getAll,
            deleteAttribute: deleteAttribute,
            editAttribute: editAttribute,
            createAttribute: createAttribute,
            getUsedAttributes: getUsedAttributes
        };

        return service;

        function getAll() {
            return attributes.$search().$then(function(_attributes) {
                return _attributes;
            }, function() {
                logger.error('Attributes not found');
            });

        }

        function editAttribute(id) {
            return attributes.$find(id).$then(function(data) {
                return data;
            }, function(reason) {
                logger.error('Attribute not found');
                $state.go('attributes.index');
            });
        }

        function createAttribute() {
            var _categories = getCategories();
            var deferred = common.$q.defer();
            var newAttribute = common.$timeout(function() {
                return attributes.$build({
                    name: '',
                    position: '10',
                    filter: 'Y',
                    template: '',
                    type: '',
                    categoryId: '',
                    categories: common.categoriesForDropdown(_categories)
                });
            }, 1000);
            deferred.resolve(newAttribute);
            return deferred.promise;
        }

        function deleteAttribute(id) {
            var attribute = attributes.$find(id);
            attribute.$destroy().$then(function() {
                logger.info('Attribute destroyed');
            }, function() {
                logger.error('Something went wrong');
            });
        }

        function getUsedAttributes(id) {
            var data = [];
            return attributes.$search({
                categoryId: id
            }).$then(function(_result) {
                angular.forEach(_result, function(value, key) {
                    data.push({
                        'name': value.name
                    });
                });
                return data;
            }, function(reason) {
                logger.error('Attributes not found');
                //$state.go('products.index');
            });
        }

        function getCategories() {
            var _categories = categories.getAll().$then(function(_c) {
                return _c;
            }, function() {
                logger.error('Cannot retrive categories');
                $state.go('attributes.index');
            });
            return _categories;
        }
    }
})();
