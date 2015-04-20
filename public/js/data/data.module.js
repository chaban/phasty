(function() {
	'use strict';

	angular.module('app.data', [
		'restmod'
	]).config(['restmodProvider',
		function(restmodProvider) {
			//restmodProvider.rebase('DefaultPacker');
			restmodProvider.rebase('AMSApi');
		}
	]);
	/*angular.module('app.data', [
		'angular-data.DS'
	]);*/
})();