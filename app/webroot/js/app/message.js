var message_app = angular.module(
    'messageApp', [
        'ui.router',
        'pascalprecht.translate',
        'ui.bootstrap',
        'jlareau.pnotify',
        'pusher-angular',
        'infinite-scroll'
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


message_app.config([
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
            .state('detail', {
                url: "/:post_id",
                templateUrl: "/template/message_detail.html",
                resolve: {
                    getPostDetail: ['$stateParams', '$http', function ($stateParams, $http) {
                        var request = {
                            method: 'GET',
                            url: cake.url.aj + $stateParams.post_id
                        };
                        return $http(request).then(function (response) {
                            return response.data;
                        });
                    }]
                },
                controller: 'MessageDetailCtrl'
            })
    }]);
