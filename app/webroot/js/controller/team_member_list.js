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

        var url = '/teams/ajax_get_team_member/';
        $http.get(url).success(function (data) {
            $scope.team_list = data.user_info;
        });

        var init = function () {

            // 名前検索のテキストボックスを表示
            $scope.name_field_show = true;
            $scope.coach_name_field_show = false;
            $scope.group_field_show = false;

            // 入力値を初期化
            $scope.name_field = '';
            $scope.group_id = null;

            // チーム全員のリストを取得する
            var url = '/teams/ajax_get_team_member_init/';
            $http.get(url).success(function (data) {
                $scope.login_user_admin_flg = data.login_user_admin_flg;
                $scope.admin_user_cnt = data.admin_user_cnt;
            });
        }
        init();

        $scope.changeFilter = function () {

            var filter_name = $scope.filter_name;

            // グループ名が選択
            if (filter_name == 'group_name') {
                init();
                $scope.name_field_show = false;
                $scope.coach_name_field_show = false;
                $scope.group_field_show = true;
                url = '/teams/ajax_get_current_team_group_list/';
                $http.get(url).success(function (data) {
                    $scope.group_list = data;
                });

            // コーチ名が選択
            } else if (filter_name == 'coach_name') {
                init();
                $scope.name_field_show = false;
                $scope.coach_name_field_show = true;
                $scope.group_field_show = false;
                var url = '/teams/ajax_get_team_member/';
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });

            //2段階認証OFF選択
            } else if (filter_name == 'two_step') {
                init();
                url = '/teams/ajax_get_current_not_2fa_step_user_list/';
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });

            // チーム管理者選択
            } else if (filter_name == 'team_admin') {
                init();
                url = '/teams/ajax_get_current_team_admin_list/';
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });

            // その他
            } else {
                init();
                var url = '/teams/ajax_get_team_member/';
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });
            }
        };

        $scope.changeGroupFilter = function () {
            var url = '/teams/ajax_get_group_member/' + $scope.group_id;
            if ($scope.group_id == null) {
                url = '/teams/ajax_get_team_member/';
            }
            $http.get(url).success(function (data) {
                $scope.team_list = data.user_info;
            });
        }

    }
);
