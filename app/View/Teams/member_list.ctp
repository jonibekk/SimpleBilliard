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

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular-route.min.js"></script>

<script type="text/javascript">
    function changeFilter() {
        var filter_name = document.getElementById('filter_name').selectedIndex;
        if (filter_name == 2) {
            document.getElementById('name_field').style.display = "none";
            document.getElementById('group_field').style.display = "block";

        } else {
            document.getElementById('name_field').style.display = "block";
            document.getElementById('group_field').style.display = "none";
        }
    }

    var app = angular.module('myApp', ['ngRoute']).
        config(['$routeProvider', function ($routeProvider) {
            $routeProvider.when('/get', {
                    controller: 'TeamController',
                    templateUrl: '/template/team_member_list.html'
                }
            )
        }]);

    app.controller("TeamController", function ($scope, $http, $location) {
        $scope.getTeamList = function () {
            var url = '/teams/ajax_get_team_member/' + $scope.name_field;
            $http.get(url).success(function (data) {
                console.log(data);
                $scope.team_list = data.user_info;
                $scope.login_user_info = data.login_user_info;
                $location.path('/get');
            });
        }
    });

</script>

<div ng-app="myApp">
    <div ng-controller="TeamController">
        <br>

        <div class="well">
            <div id="filter_box" class="row">
                <div class="col-xs-12 col-sm-4">
                    <select id="filter_name" name="filter_name" class="form-control" onchange="changeFilter()">
                        <option value="all">すべて</option>
                        <option value="coach_name">コーチの名前</option>
                        <option value="group_name">グループ名</option>
                        <option value="team_admin">チーム管理者</option>
                        <option value="invite" ng-if="login_user_info.admin_flg == true">招待中</option>
                        <option value="two_step" ng-if="login_user_info.admin_flg == true">2段階認証OFF</option>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-8">
                    <div id="name_field">
                        <input ng-model="name_field" ng-keypress="getTeamList()" type="text" class="form-control"
                               placeholder="名前を入力してください">
                    </div>
                    <div id="group_field" style="display: none">
                        <select name="group_id" class="form-control">
                            <option value="all">すべて</option>
                            <?php foreach ($group_info as $id => $name) { ?>
                                <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="">
                    無効化されたメンバーも表示する
                    <!-- <p><?= $ui['TeamMember']['active_flg'] == (string)TeamMember::ACTIVE_USER_FLAG ? '在職中' : '退職'; ?></p> -->
                </label>
            </div>
        </div>

        <div class="row" ng-view></div>
    </div>
</div>
