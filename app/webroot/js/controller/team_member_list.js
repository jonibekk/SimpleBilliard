var app = angular.module('myApp', ['ngRoute']).
    config(['$routeProvider', function ($routeProvider) {
        $routeProvider
            .when('/', {
                controller: 'TeamMemberMainController',
                templateUrl: '/template/team_member_list.html'
            });
    }]);

app.controller("TeamMemberMainController", function ($scope, $http) {

        // 無効化されたメンバーも表示する項目
        $scope.disp_active_flag = '1';

        var showTextField = function () {
            $scope.name_field_show = true;
            $scope.group_field_show = false;
        }
        showTextField();

        var init = function () {
            var url = '/teams/ajax_get_team_member_init/';
            $http.get(url).success(function (data) {
                $scope.login_user_info = data.login_user_info;
            });

            var url = '/teams/ajax_get_team_member/';
            $http.get(url).success(function (data) {
                $scope.team_list = data.user_info;
            });
        };
        init();

        $scope.changeFilter = function () {
            var filter_name = $scope.filter_name;
            if (filter_name == 'group_name') {
                var url = '/teams/ajax_get_current_team_group_list/';
                $http.get(url).success(function (data) {
                    $scope.group_list = data;
                });
                $scope.name_field_show = false;
                $scope.group_field_show = true;
                init();
            } else if (filter_name == 'coach_name') {
                $scope.name_field = '';

            } else if (filter_name == 'two_step') {
                $scope.name_field = '';
                showTextField();
                var url = '/teams/ajax_get_current_not_2fa_step_user_list/';
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });
            } else if (filter_name == 'team_admin') {
                $scope.name_field = '';
                showTextField();
                var url = '/teams/ajax_get_current_team_admin_list/';
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });
            } else {
                init();
                $scope.name_field = '';
                showTextField();
            }
        };

        $scope.changeGroupFilter = function () {
            var url = '/teams/ajax_get_group_member/' + $scope.group_id;
            if ($scope.group_id == null) {
                var url = '/teams/ajax_get_team_member/';
            }
            $http.get(url).success(function (data) {
                $scope.team_list = data.user_info;
            });
        }

    }
);
