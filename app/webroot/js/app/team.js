var app = angular.module('myApp', ['ui.router', 'pascalprecht.translate']);

app.config(['$stateProvider', '$urlRouterProvider', '$translateProvider',
    function ($stateProvider, $urlRouterProvider, $translateProvider) {
        $urlRouterProvider.otherwise("/");

        $stateProvider
            .state('member', {
                url: "/",
                templateUrl: "/template/team_member_list.html",
                controller: 'TeamMemberMainController'
            })
            .state('vision', {
                url: "/vision/:team_id",
                templateUrl: "/template/team_vision_list.html",
                controller: 'TeamVisionController',
                resolve: {
                    teamVisionList: ['$stateParams', '$http', function ($stateParams, $http) {
                        var request = {
                            method: 'GET',
                            url: cake.url.u + $stateParams.team_id
                        }
                        return $http(request).then(function (response) {
                            return response.data;
                        })

                    }]
                }

            })
            .state('vision_archive', {
                url: "/vision_archive/:team_id/:active_flg",
                templateUrl: "/template/team_vision_list.html",
                controller: 'TeamVisionArchiveController',
                resolve: {
                    teamVisionArchiveList: ['$stateParams', '$http', function ($stateParams, $http) {
                        var request = {
                            method: 'GET',
                            url: cake.url.u + $stateParams.team_id + '/' + $stateParams.active_flg
                        }
                        return $http(request).then(function (response) {
                            return response.data;
                        })

                    }]
                }
            })
            .state('set_vision_archive', {
                url: "/set_vision_archive/:team_vision_id/:active_flg",
                resolve: {
                    setVisionArchive: ['$stateParams', '$http', function ($stateParams, $http) {
                        var request = {
                            method: 'GET',
                            url: cake.url.v + $stateParams.team_vision_id + '/' + $stateParams.active_flg
                        }
                        return $http(request).then(function (response) {
                            return response.data;
                        })
                    }]
                },
                controller: function ($state) {
                    $state.go('vision', {team_id: 1});
                }
            })
            .state('group_vision', {
                url: "/group_vision",
                templateUrl: "/template/group_vision_list.html",
                controller: ''
            });

        $translateProvider.useStaticFilesLoader({
            prefix: '/i18n/locale-',
            suffix: '.json'
        });
        $translateProvider.preferredLanguage('ja');
        $translateProvider.fallbackLanguage('en');
    }]);
