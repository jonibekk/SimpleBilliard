var message_list_app = angular.module(
    'messageListApp', [
        'ui.router',
        'pascalprecht.translate',
        'ui.bootstrap',
        'jlareau.pnotify',
        'pusher-angular',
        'infinite-scroll',
        'ngSanitize'
    ]).run([
        '$rootScope',
        '$state',
        '$stateParams',
        '$http',
        '$translate',
        function ($rootScope,
                  $state,
                  $stateParams,
                  $http,
                  $translate) {
            $rootScope.$state = $state;
            $rootScope.$stateParams = $stateParams;
        }]
);


message_list_app.config([
    '$stateProvider',
    '$urlRouterProvider',
    '$translateProvider',
    '$httpProvider',
    function ($stateProvider,
              $urlRouterProvider,
              $translateProvider,
              $httpProvider) {

        $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

        $urlRouterProvider.otherwise("/");
        $stateProvider
            .state('list', {
                url: "/",
                templateUrl: "/template/message_list.html",
                controller: 'MessageListCtrl'
            })
    }]);
