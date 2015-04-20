(function() {
    'use strict';

    angular
        .module('app.data')
        .factory('currency.model', CurrencysModel);

    CurrencysModel.$inject = ['restmod', 'logger', '$state'];

    function CurrencysModel(restmod, logger, $state) {
        var currency = restmod.model('/admin/currencies/').mix({
            $config: {
                name: 'currency',
                plural: 'currencies'
            }
        });
        //var collection = currency.$collection();

        var service = {
            getAll: getAll,
            deleteCurrency: deleteCurrency,
            editCurrency: editCurrency,
            createCurrency: createCurrency
        };

        return service;

        function getAll() {
            return currency.$search();
        }

        function editCurrency(id) {
            return currency.$find(id).$then(function(_currency) {
                return _currency;
            }, function(reason) {
                logger.error('Currency not found');
                $state.go('currency.index');
            });
        }

        function createCurrency() {
            return currency.$build({
                name: '',
                rate: '',
            });
        }

        function deleteCurrency(id) {
            var _currency = currency.$find(id);
            _currency.$destroy().$then(function() {
                logger.info('Currency destroyed');
            }, function() {
                logger.error('Something went wrong');
            });
        }
    }
})();
