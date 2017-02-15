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
                  $stateParams
        ) {
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
        // Anti IE cache
        if (!$httpProvider.defaults.headers.get)
            $httpProvider.defaults.headers.get = {};
        $httpProvider
            .defaults
            .headers
            .get['If-Modified-Since'] = (new Date(0)).toUTCString();
        $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

        $urlRouterProvider.otherwise(function(){
            location.href = "/posts/message_list";
        });
        $stateProvider
            .state('list', {
                url: "",
                templateUrl: "/template/message_list.html",
                controller: 'MessageListCtrl'
            })
    }]);
