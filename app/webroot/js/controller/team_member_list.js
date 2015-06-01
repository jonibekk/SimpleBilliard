var app = angular.module('myApp', ['ngRoute']).
    config(['$routeProvider', function ($routeProvider) {
        $routeProvider
            .when('/', {
                controller: 'TeamMemberMainController',
                templateUrl: '/template/team_member_list.html'
            });
    }]);

app.controller("TeamMemberMainController", function ($scope, $http) {

        $scope.count = 0;
        // 無効化されたメンバーも表示する項目
        $scope.disp_active_flag = '1';
        // 名前入力欄
        $scope.name_field_show = true;
        // グループ選択欄
        $scope.group_field_show = false;

        var init = function () {
            var url = '/teams/ajax_get_team_member_init/';
            $http.get(url).success(function (data) {
                $scope.login_user_info = data.login_user_info;
            });

            var url = '/teams/ajax_get_team_member/';
            $http.get(url).success(function (data) {
                $scope.team_list = data.user_info;
                $scope.count = data.count;
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
            } else if (filter_name == 'coach_name') {

            } else if (filter_name == 'team_admin') {
                // チーム管理者選択
                var url = '/teams/ajax_get_current_team_admin_list/';
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                    $scope.count = data.count;
                });
            } else {
                init();
            }
        };

        $scope.changeGroupFilter = function () {
            var url = '/teams/ajax_get_group_member/' + $scope.group_id;
            $http.get(url).success(function (data) {
                console.log(data);
                $scope.team_list = data.user_info;
            });
        }

    }
);
