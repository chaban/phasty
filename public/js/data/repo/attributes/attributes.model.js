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
            createAttribute: createAttribute

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
            return attributes.$find(id).$then(function(_attribute) {
                return _attribute;
            }, function(reason) {
                logger.error('Attribute not found');
                $state.go('attributes.index');
            });
        }

        function createAttribute() {
            var _categories = categories.getAll().$then(function(_c) {
                console.log(_c);
                return _c;
            }, function() {
                logger.error('Cannot retrive categories');
                $state.go('attributes.index');
            });
            var deferred = common.$q.defer();
            var newAttribute = common.$timeout(function() {
                return attributes.$build({
                    name: '',
                    position: '1',
                    categoryId: '',
                    categories: _categories
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
    }
})();
