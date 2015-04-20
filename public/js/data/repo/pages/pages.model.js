(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('pages.model', PagesModel);

    PagesModel.$inject = ['restmod', 'logger', '$state'];

    function PagesModel(restmod, logger, $state) {
        var pages = restmod.model('/admin/page/');
        //var collection = pages.$collection();

        var service = {
            byPage: byPage,
            deletePage: deletePage,
            editPage: editPage,
            createPage: createPage
        };

        return service;

        function byPage(currentPage, pageItems, filterBy, filterByFields, orderBy, orderByReverse) {
            var order = 'asc';
            if (!orderByReverse) {
                order = 'desc';
            }
            return pages.$search({
                page: currentPage,
                limit: pageItems,
                orderBy: orderBy,
                filterByFields: filterByFields,
                order: order
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
