(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('pages.model', PagesModel);

    PagesModel.$inject = ['restmod', 'logger', '$state'];

    function PagesModel(restmod, logger, $state) {
        var pages = restmod.model('/admin/pages/');
        //var collection = pages.$collection();

        var service = {
            getAll: getAll,
            deletePage: deletePage,
            editPage: editPage,
            createPage: createPage
        };

        return service;

        function getAll() {
            return pages.$search().$then(function(_pages) {
                return _pages;
            }, function() {
                logger.error('Pages not found');
            });
        }

        function editPage(id) {
            var page = pages.$find(id).$then(function(_page) {
                return _page;
            }, function(reason) {
                logger.error('Page not found');
                $state.go('pages.index');
            });
            return page;
        }

        function createPage() {
            return pages.$build({
                name: '',
                content: '',
                seoDescription: '',
                seoKeywords: '',
                active: 'Y'
            });
        }

        function deletePage(id) {
            var page = pages.$find(id);
            page.$destroy().$then(function() {
                logger.info('Page destroyed');
            }, function() {
                logger.error('Something went wrong');
            });
        }
    }
})();
