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
                .when('/', {
                    controller: 'TeamMemberMainController',
                    templateUrl: '/template/team_member_list.html'
                });
        }]);

    app.controller("TeamMemberMainController", function ($scope, $http) {

            var init = function () {
                $scope.name_field_show = true;
                $scope.group_field_show = false;
                var url = '/teams/ajax_get_team_member_init/';
                $http.get(url).success(function (data) {
                    $scope.login_user_info = data.login_user_info;
                });
            };
            init();

            var url = '/teams/ajax_get_team_member/';
            $http.get(url).success(function (data) {
                $scope.team_list = data.user_info;
            });

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
                } else {
                    init();
                }
            };

            $scope.getTeamList = function () {
                var url = '/teams/ajax_get_team_member/' + $scope.name_field;
                $http.get(url).success(function (data) {
                    $scope.team_list = data.user_info;
                });
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
</script>

<div ng-app="myApp">
    <div ng-controller="TeamMemberMainController" ng-view></div>
</div>
