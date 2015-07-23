var message_app = angular.module('messageApp', ['ui.router', 'pascalprecht.translate', 'ui.bootstrap', 'jlareau.pnotify'])
    .run(['$rootScope', '$state', '$stateParams', '$http', '$translate',
        function ($rootScope, $state, $stateParams, $http, $translate) {
            $rootScope.$state = $state;
            $rootScope.$stateParams = $stateParams;
        }]
);


message_app.config(['$stateProvider', '$urlRouterProvider', '$translateProvider', '$httpProvider',
    function ($stateProvider, $urlRouterProvider, $translateProvider, $httpProvider) {

        $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

        $urlRouterProvider.otherwise("/");
        $stateProvider
            .state('detail', {
                url: "/",
                templateUrl: "/template/message_detail.html",
                controller: 'MessageDetailCtrl'
            })
}]);
