<!-- START app/View/Teams/member_list.ctp -->
<style type="text/css">
    .team_member_table {
        background-color: #ffffff;
        font-size: 14px;
        border-radius: 5px;
    }

    .team_member_count_label {
        font-size: 18px;
        padding: 12px;
    }

    .team_member_setting_btn {
        color: #ffffff;
        background-color: #ffffff;
        border-color: lightgray;
    }

</style>

<?php
echo $this->Html->script('vendor/angular/angular.min');
echo $this->Html->script('vendor/angular/angular-route.min');
?>

<script type="text/javascript">

    var app = angular.module('myApp', ['ngRoute']).
        config(['$routeProvider', function ($routeProvider) {
            $routeProvider
                /*
                .when('/', {
                    controller: 'TeamMemberListController',
                    templateUrl: '/template/team_member_list.html'
                })
                */
                .when('/get', {
                    controller: 'TeamMemberListController',
                    templateUrl: '/template/team_member_list.html'
                })
        }]);

    app.controller("TeamMemberListController", function ($scope, $http, $location) {

        var init = function () {
            $scope.name_field_show = true;
            $scope.group_field_show = false;
            var url = '/teams/ajax_get_team_member_init/';
            $http.get(url).success(function (data) {
                $scope.res = data;
                console.log(data);
            });
        }
        init();

        $scope.getTeamList = function () {
            var url = '/teams/ajax_get_team_member/' + $scope.name_field;
            $http.get(url).success(function (data) {
                $scope.team_list = data.user_info;
                $scope.login_user_info = data.login_user_info;
                $location.path('/get');
            });
        }

        $scope.changeFilter = function () {
            var filter_name = $scope.filter_name;
            if (filter_name == 'group_name') {
                var url = '/teams/ajax_get_current_team_group_list/';
                $http.get(url).success(function (data) {
                    $scope.group_list = data;
                });
                $scope.name_field_show = false;
                $scope.group_field_show = true;
            } else {
                init();
            }
        }
    });

</script>

<div ng-app="myApp">
    <div ng-controller="TeamMemberListController">
        <br>

        <div class="well">
            <div id="filter_box" class="row">
                <div class="col-xs-12 col-sm-4">
                    <select ng-model="filter_name" name="filter_name" class="form-control" ng-change="changeFilter()">
                        <option ng-selected="true" value="">すべて</option>
                        <option value="coach_name">コーチの名前</option>
                        <option value="group_name">グループ名</option>
                        <option value="team_admin">チーム管理者</option>
                        <option value="invite" ng-if="res.login_user_info.admin_flg == true">招待中</option>
                        <option value="two_step" ng-if="res.login_user_info.admin_flg == true">2段階認証OFF</option>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-8">
                    <div ng-show="name_field_show">
                        <input ng-model="name_field" ng-keypress="getTeamList()" type="text" class="form-control"
                               placeholder="名前を入力してください">
                    </div>
                    <select ng-show="group_field_show" ng-model="select_group_id"
                            ng-options="num as name for (num, name) in group_list" class="form-control">
                        <option ng-selected="true" value="">すべて</option>
                    </select>
                </div>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="">
                    無効化されたメンバーも表示する
                </label>
            </div>
        </div>

        <div class="row" ng-view></div>
    </div>
</div>
