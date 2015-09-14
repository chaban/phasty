(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('index.model', IndexModel);

    IndexModel.$inject = ['restmod', 'logger', 'common', 'AuthTokenFactory', 'jwtHelper'];

    function IndexModel(restmod, logger, common, auth, jwtHelper) {

        var dashboard = restmod.model('/admin/dashboard');
        //var collection = Index.$collection();

        var service = {
            getDashboardData: getDashboardData,
            setJwtToken: setJwtToken,
            getTokenInfo: getTokenInfo
        };

        return service;

        function getDashboardData() {
            return dashboard.$search().$then(function(data) {
                return data;
            }, function() {
                logger.error('there is no data');
            });
        }

        function setJwtToken() {
            return dashboard.$search({
                needToken: 'yes'
            }).$then(function(data) {
                //var token = data.$response.data.dashboards[0].token;
                return auth.setToken(data.$response.data.dashboards[0].token);
                //return jwtHelper.decodeToken(token);
            }, function() {
                logger.error('Could not get token');
            });
        }

        function getTokenInfo() {
            var token = auth.getToken();
            return jwtHelper.decodeToken(token);
        }
    }
})();
