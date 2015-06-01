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
            $scope.count = 0;
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
            $scope.count = data.count;
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
                $scope.count = data.count;
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
