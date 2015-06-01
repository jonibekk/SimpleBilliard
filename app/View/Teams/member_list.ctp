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
echo $this->Html->script('controller/team_member_list');
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
</script>

<div ng-app="myApp">
    <div ng-controller="TeamMemberMainController" ng-view> 検索中....</div>
</div>
