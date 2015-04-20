(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('categories.model', CategoriesModel);

    CategoriesModel.$inject = ['restmod', 'logger', '$state', '$http', 'common'];

    function CategoriesModel(restmod, logger, $state, $http, common) {
        var categories = restmod.model('/admin/categories').mix({
            $config: {
                name: 'category',
                plural: 'categories'
            }
        });
        //var collection = categories.$collection();

        var service = {
            getAll: getAll,
            updateCategories: updateCategories,
            //saveAll: saveAll,
            deleteCategory: deleteCategory
        };

        return service;

        function getAll() {
            return categories.$search().$then(function(_categories) {
                return _categories.$response.data.categories;
            });
        }

        function updateCategories(item) {
            var Category;
            if (undefined !== item.parent_id) {
                Category = categories.$find(item.parent_id).$then(function(_c) {
                    _c.children = item;
                    return _c;
                });
            } else if (undefined !== item.id) {
                Category = categories.$find(item.id).$then(function(_c) {
                    _c.title = item.title;
                    return _c;
                });
            } else {
                return logger.error('not valid item');
            }
            //removeMetaInformation(all);
            Category.$save().$then(function() {
                logger.info('category ' + item.title + ' saved');
            }, function() {
                logger.error('Something went wrong');
            });
        }

        /*function saveAll(all) {
            removeMetaInformation(all);
            var promise = $http({
                method: 'put',
                url: '/admin/categories/1',
                data: all
            });
            promise.success(function(data, status, headers, config) {
                logger.success('all categories saved');
            });
            promise.error(function(data, status, headers, config) {
                logger.error('categories not saved');
            });
            return all;//refresh categories array without reloading page
        }*/

        function deleteCategory(id, title) {
            var Category = categories.$find(id);
            Category.$destroy().$then(function() {
                logger.info('category ' + title + ' deleted');
            }, function() {
                logger.error('Something went wrong');
            });
        }

        //remove meta information from all categories array
        /*function removeMetaInformation(all) {
            angular.forEach(all, function(value, key) {
                if ((undefined !== value.children) && (undefined !== value.children.length)) removeMetaInformation(value.children);
                delete value.newItem;
                delete value.editing;
                delete value.parent_id;
            });
        }*/
    }
})();
