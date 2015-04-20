(function() {
    'use strict';

    angular
        .module('app.categories')
        .controller('CategoriesController', CategoriesController);

    CategoriesController.$inject = ['categories.model', 'logger', '$translate', 'common'];

    function CategoriesController(categories, logger, $translate, common) {
        /*jshint validthis: true */
        var vm = this;
        //vm.saveAll = saveAll;
        vm.updateCategories = updateCategories;
        vm.deleteResource = deleteResource;
        vm.newRootCategory = newRootCategory;
        vm.newSubCategory = newSubCategory;
        vm.modalRemoveMessage = 'Are you sure to remove this item?';
        vm.modalWarningMessage = 'Save data first!';

        activate();

        function activate() {
            $translate('Categories.Title').then(function(tr) {
                vm.title = tr;
            });
            $translate('Confirm_Delete').then(function(tr) {
                vm.modalMessage = tr;
            });
            vm.children = categories.getAll();
            //vm.showSave = true;
        }

        function updateCategories(child) {
            //console.log(child);
            categories.updateCategories(child);
            return refresh();
        }

        function newRootCategory(title) {
            if (!title) {
                return window.alert('Enter category name first');
            }
            var item = {};
            item.title = title;
            item.parent_id = 1;
            return updateCategories(item);
        }

        function newSubCategory(scope, child) {
            var nodeData = scope.$modelValue;
            if (!nodeData.id) {
                return window.alert(vm.modalWarningMessage);
            }
            var newCategory = {
                title: nodeData.title + '.' + (nodeData.id + 1),
                parent_id: nodeData.id,
                children: [],
                newItem: true,
                editing: true
            };
            if (!nodeData.children) {
                nodeData.children = [];
            }
            return nodeData.children.push(newCategory);
        }

        /*function saveAll() {
            vm.children = categories.saveAll(vm.children);
        }*/

        function deleteResource(item) {
            if (window.confirm(vm.modalMessage)) {
                if (!item.$modelValue.newItem) {
                    categories.deleteCategory(item.$modelValue.id, item.$modelValue.title);
                }
                item.remove();
            }
            return refresh();
        }

        function refresh() {
            common.$timeout(function() {
                vm.children = categories.getAll();
            }, 1000);
        }
    }
})();
