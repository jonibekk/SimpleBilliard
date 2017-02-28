var message_app = angular.module(
    'messageApp', [
        'ui.router',
        'pascalprecht.translate',
        'ui.bootstrap',
        'jlareau.pnotify',
        'pusher-angular',
        'infinite-scroll',
        'ngSanitize',
        'ngRoute'
    ]).run([
    '$rootScope',
    '$state',
    '$stateParams',
    function ($rootScope,
              $state,
              $stateParams
    ) {
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
              $httpProvider
    ) {
        //Anti IE cache
        if (!$httpProvider.defaults.headers.get)
            $httpProvider.defaults.headers.get = {};
        $httpProvider
            .defaults
            .headers
            .get['If-Modified-Since'] = (new Date(0)).toUTCString();
        $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

        $translateProvider.useStaticFilesLoader({
            prefix: '/i18n/locale-',
            suffix: '.json'
        });
        $translateProvider.preferredLanguage('ja');
        $translateProvider.fallbackLanguage('en');

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
            .state('detail_from_email', {
                url: "/:post_id/:from",
                resolve: {
                    detailFromEmail: ['$stateParams', '$location', function ($stateParams, $location) {
                        $location.path('/' + $stateParams.post_id)
                    }]
                }
            })
    }]);
