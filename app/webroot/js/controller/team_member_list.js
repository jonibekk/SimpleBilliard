var app = angular.module('myApp', ['ngRoute', 'pascalprecht.translate']).

    config(['$routeProvider', '$translateProvider', function ($routeProvider, $translateProvider) {
        $routeProvider
            .when('/', {
                controller: 'TeamMemberMainController',
                templateUrl: '/template/team_member_list.html'
            });

        $translateProvider.useStaticFilesLoader({
            prefix: '/i18n/locale-',
            suffix: '.json'
        });
        $translateProvider.preferredLanguage('ja');
        $translateProvider.fallbackLanguage('en');
    }]);

app.controller("TeamMemberMainController", function ($scope, $http, $translate) {

        $scope.disp_active_flag = '1';

        var url = cake.url.i;
        $http.get(url).success(function (data) {
            $scope.team_list = data.user_info;
        });

        var init = function () {

            $scope.name_field_show = true;
            $scope.coach_name_field_show = false;
            $scope.group_field_show = false;

            $scope.name_field = '';
            $scope.group_id = null;

            var url = cake.url.j;
            $http.get(url).success(function (data) {

                $scope.login_user_id = data.login_user_id;
                $scope.login_user_admin_flg = data.login_user_admin_flg;

                $scope.login_user_language = data.login_user_language;
                if ($scope.login_user_language === 'eng') {
                    $translate.use('en');
                }

                $scope.admin_user_cnt = data.admin_user_cnt;
            });
        };
        init();

        $scope.changeFilter = function () {

            var filter_name = $scope.filter_name;

            if (filter_name === 'group_name') {
                init();
                $scope.name_field_show = false;
                $scope.coach_name_field_show = false;
                $scope.group_field_show = true;
                url = cake.url.k;
                $http.get(url).success(function (data) {
                    $scope.group_list = data;
                });

            } else if (filter_name === 'coach_name') {
                init();
                $scope.name_field_show = false;
                $scope.coach_name_field_show = true;
                $scope.group_field_show = false;
                url = cake.url.i;
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });

            } else if (filter_name === 'two_step') {
                init();
                url = cake.url.l;
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });

            } else if (filter_name === 'team_admin') {
                init();
                url = cake.url.m;
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });

            } else {
                init();
                var url = cake.url.i;
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });
            }
        };

        $scope.changeGroupFilter = function () {
            var url = cake.url.n + $scope.group_id;
            if ($scope.group_id === null) {
                url = cake.url.i;
            }
            $http.get(url).success(function (data) {
                $scope.team_list = data.user_info;
            });
        };

        $scope.setActiveFlag = function (index, member_id, active_flg) {
            var url = cake.url.o + member_id + '/' + active_flg;
            $http.get(url).success(function (data) {
                var active_show_flg = false;
                if (active_flg === 'ON') {
                    active_show_flg = true;
                }
                $scope.team_list[index]['TeamMember']['active_flg'] = active_show_flg;
            });
        };

        $scope.setAdminUserFlag = function (index, member_id, admin_flg) {
            var url = cake.url.p + member_id + '/' + admin_flg;
            $http.get(url).success(function (data) {

                var admin_show_flg = false;
                if (admin_flg === 'ON') {
                    admin_show_flg = true;
                    $scope.admin_user_cnt = $scope.admin_user_cnt + 1;

                } else if (admin_flg === 'OFF') {
                    admin_show_flg = false;
                    $scope.admin_user_cnt = $scope.admin_user_cnt - 1;

                    if ($scope.login_user_id == $scope.team_list[index]['User']['id']) {
                        $scope.login_user_admin_flg = false;
                    }
                }

                $scope.team_list[index]['TeamMember']['admin_flg'] = admin_show_flg;
            });
        };

        $scope.setEvaluationFlag = function (index, member_id, evaluation_flg) {

            var url = cake.url.q + member_id + '/' + evaluation_flg;
            $http.get(url).success(function (data) {

                var show_evaluation_flg = false;
                if (evaluation_flg == 'ON') {
                    show_evaluation_flg = true;
                }

                $scope.team_list[index]['TeamMember']['evaluation_enable_flg'] = show_evaluation_flg;
            });

        }
    }
);
