(function() {
    'use strict';

    angular
        .module('app.core')
        .factory('modal.dialog', mkModalDialog);

    mkModalDialog.$inject = ['$templateCache'];

    function mkModalDialog($templateCache) {
        var service = {
            deleteDialog: deleteDialog,
            confirmationDialog: confirmationDialog
        };

        $templateCache.put('mkModalDialog.tpl.html',
            '<div class="modal" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog">' +
            '<div class="modal-content">' +
            '<div class="modal-header" ng-show="title">' +
            '<button type="button" class="close" ng-click="$hide()">&times;</button>' +
            '<h4 class="modal-title" ng-bind="title"></h4>' +
            '</div>' +
            '<div class="modal-body" ng-bind="content"></div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-default" ng-click="$hide()" translate="CancelText"></button>' +
            '<button type="button" class="btn btn-danger" ng-click="vm.deleteResource(gridItem);vm.tableItems.splice(vm.tableItems.indexOf(gridItem), 1);$event.stopPropagation()" translate="OkText"></button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');

        return service;

        function deleteDialog(title, del, itemName) {
            var item = 'Item';
            itemName = itemName || item;
            var msg = del + '<br />' + itemName + '?';

            return confirmationDialog(title, msg);
        }

        function confirmationDialog(title, content) {
            var modalOptions = {
                //template: 'mkModalDialog.tpl.html',
                title: title,
                content: content,
                animation: 'am-fade-and-scale',
                placement: 'top',
                html: true,
                backdrop: 'static',
                show: false
            };

            return modalOptions;
        }
    }
})();