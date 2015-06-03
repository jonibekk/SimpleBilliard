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

        var url = cake['url']['i'];
        $http.get(url).success(function (data) {
            console.log(data);
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
            var url = cake['url']['j'];
            $http.get(url).success(function (data) {
                $scope.login_user_id = data.login_user_id;
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
                url = cake['url']['k'];
                $http.get(url).success(function (data) {
                    $scope.group_list = data;
                });

                // コーチ名が選択
            } else if (filter_name == 'coach_name') {
                init();
                $scope.name_field_show = false;
                $scope.coach_name_field_show = true;
                $scope.group_field_show = false;
                url = cake['url']['i'];
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });

                //2段階認証OFF選択
            } else if (filter_name == 'two_step') {
                init();
                url = cake['url']['l'];
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });

                // チーム管理者選択
            } else if (filter_name == 'team_admin') {
                init();
                url = cake['url']['m'];
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });

                // その他
            } else {
                init();
                var url = cake['url']['i'];
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });
            }
        };

        $scope.changeGroupFilter = function () {
            var url = cake['url']['n'] + $scope.group_id;
            if ($scope.group_id == null) {
                url = cake['url']['i'];
            }
            $http.get(url).success(function (data) {
                $scope.team_list = data.user_info;
            });
        }

        $scope.setActiveFlag = function (index, member_id, active_flg) {
            var url = cake['url']['o'] + member_id + '/' + active_flg;
            $http.get(url).success(function (data) {
                var show_flg = false;
                if (active_flg == 'ON') {
                    show_flg = true;
                }
                $scope.team_list[index]['TeamMember']['active_flg'] = show_flg;
            });
        }

        $scope.setAdminUserFlag = function (index, member_id, admin_flg) {
            var url = cake['url']['p'] + member_id + '/' + admin_flg;
            $http.get(url).success(function (data) {

                if (admin_flg == 'ON') {
                    // 設定ボタンの表示切り替え
                    var show_flg = true;
                    $scope.admin_user_cnt = $scope.admin_user_cnt + 1;

                } else if (admin_flg == 'OFF') {
                    var show_flg = false;
                    $scope.admin_user_cnt = $scope.admin_user_cnt - 1;

                    // 自分を管理者から外した場合はすべての設定ボタンを表示しない
                    if ($scope.login_user_id == $scope.team_list[index]['User']['id']) {
                        $scope.login_user_admin_flg = false;
                    }
                }

                // 項目の切り替えのため(管理者にする or 管理者から外す)
                $scope.team_list[index]['TeamMember']['admin_flg'] = show_flg;
            });
        }

        $scope.setEvaluationFlag = function (index, member_id, evaluation_flg) {

            var url = cake['url']['q'] + member_id + '/' + evaluation_flg;
            $http.get(url).success(function (data) {

                var show_evaluation_flg = false;
                if (evaluation_flg == 'ON') {
                    show_evaluation_flg = true;
                }

                // 項目の切り替えのため(管理者にする or 管理者から外す)
                $scope.team_list[index]['TeamMember']['evaluation_enable_flg'] = show_evaluation_flg;
            });

        }
    }
);
