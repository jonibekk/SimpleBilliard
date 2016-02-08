var message_app = angular.module(
    'messageApp', [
        'ui.router',
        'pascalprecht.translate',
        'ui.bootstrap',
        'jlareau.pnotify',
        'pusher-angular',
        'infinite-scroll',
        'ngSanitize',
        'ngRoute',
        'ngLocationUpdate'
    ]).run([
    '$rootScope',
    '$state',
    '$stateParams',
    '$http',
    '$translate',
    '$route',
    '$location',
    function ($rootScope,
              $state,
              $stateParams,
              $http,
              $translate,
              $route,
              $location
    ) {
        $rootScope.$state = $state;
        $rootScope.$stateParams = $stateParams;

        var original = $location.path;
        $location.path = function (path, reload) {
            if (reload === false) {
                var lastRoute = $route.current;
                var un = $rootScope.$on('$locationChangeSuccess', function () {
                    $route.current = lastRoute;
                    un();
                });
            }
            return original.apply($location, [path]);
        };
    }]
);

message_app.config([
    '$stateProvider',
    '$urlRouterProvider',
    '$translateProvider',
    '$httpProvider',
    '$locationProvider',
    function ($stateProvider,
              $urlRouterProvider,
              $translateProvider,
              $httpProvider,
              $locationProvider
    ) {
        //$locationProvider.html5Mode(true);
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

        //$urlRouterProvider.otherwise("^/");
        //$urlRouterProvider.otherwise(function ($injector, $location) {
        //    console.log("FURU:otherwise:detail");
        //    console.log($location);
        //    console.log($location.path());
        //    console.log($location.url);
        //    alert($location.url);
        //});
        $stateProvider
            .state('detail', {
                url: "/:post_id",
                templateUrl: "/template/message_detail.html",
                resolve: {
                    getPostDetail: ['$stateParams', '$http', '$location', function ($stateParams, $http, $location) {
                        console.log("FURU#1:stateProvider#detail");
                        console.log($stateParams);
                        console.log($location.path());
                        //alert("stop!");
                        if (!$stateParams.post_id) {
                            $stateParams.post_id = angular_message_post_id;
                            //if(last_angular_message_post_id == angular_message_post_id){
                            //    console.log("already called.");
                            //    return;
                            //}
                        }
                        console.log($stateParams);

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
            .state('detail2', {
                url: "",
                templateUrl: "/template/message_detail.html",
                resolve: {
                    getPostDetail: ['$stateParams', '$http', function ($stateParams, $http) {
                        console.log("FURU#2:stateProvider#detail2");
                        console.log($stateParams);
                        //alert("stop!#2");
                        if (!$stateParams.post_id) {
                            $stateParams.post_id = angular_message_post_id;
                            //if(last_angular_message_post_id == angular_message_post_id){
                            //    console.log("already called.");
                            //    return;
                            //}
                        }
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
            .state('detail3', {
                url: "^/posts/message/:post_id",
                templateUrl: "/template/message_detail.html",
                resolve: {
                    getPostDetail: ['$stateParams', '$http', function ($stateParams, $http) {
                        console.log("FURU#3:stateProvider#detail3");
                        console.log($stateParams);
                        //alert("stop!#2");
                        if (!$stateParams.post_id) {
                            $stateParams.post_id = angular_message_post_id;
                            //if(last_angular_message_post_id == angular_message_post_id){
                            //    console.log("already called.");
                            //    return;
                            //}
                        }
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


