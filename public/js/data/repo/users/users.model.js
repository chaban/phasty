(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('users.model', UsersModel);

    UsersModel.$inject = ['restmod', 'logger', '$state'];

    function UsersModel(restmod, logger, $state) {
        var users = restmod.model('/admin/users');
        //var collection = users.$collection();

        var service = {
            byUser: byUser,
            deleteUser: deleteUser,
            editUser: editUser,
            createUser: createUser
        };

        return service;

        function byUser(currentPage, pageItems, filterBy, filterByFields, orderBy, orderByReverse) {
            var order = 'asc';
            if (!orderByReverse) {
                order = 'desc';
            }
            return users.$search({
                page: currentPage,
                limit: pageItems,
                orderBy: orderBy,
                filterByFields: filterByFields,
                order: order
            });
        }

        function editUser(id) {
            var user = users.$find(id).$then(function(_user) {
                return _user;
            }, function(reason) {
                logger.error('User not found');
                $state.go('users.index');
            });
            return user;
        }

        function createUser() {
            return users.$build({
                name: '',
                email: '',
                password: '',
                address: '',
                phone: '',
                commentsCount: 0,
                createdAt: '',
                lastLogin: '',
                mustChangePassword: 'N',
                banned: 'N',
                confirmed: 'Y'
            });
        }

        function deleteUser(id) {
            var user = users.$find(id);
            user.$destroy().$then(function() {
                logger.info('User destroyed');
            }, function() {
                logger.error('Something went wrong');
            });
        }
    }
})();
