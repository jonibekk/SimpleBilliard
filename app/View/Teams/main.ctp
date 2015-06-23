<?
/**
 * @var CodeCompletionView $this
 */
?>
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

    .disable_member {
        background-color: lightgray;
    }

</style>

<script>
    var app = angular.module('myApp', ['ui.router', 'pascalprecht.translate']);

    app.config(['$stateProvider', '$urlRouterProvider', '$translateProvider',
        function ($stateProvider, $urlRouterProvider, $translateProvider) {
            $urlRouterProvider.otherwise("/");

            $stateProvider
                .state('member', {
                    url: "/",
                    templateUrl: "/template/team_member_list.html",
                    controller: 'TeamMemberMainController'
                })
                .state('vision', {
                    url: "/vision",
                    templateUrl: "/template/team_vision_list.html",
                    controller: ''
                })
                .state('group_vision', {
                    url: "/group_vision",
                    templateUrl: "/template/group_vision_list.html",
                    controller: ''
                });

            $translateProvider.useStaticFilesLoader({
                prefix: '/i18n/locale-',
                suffix: '.json'
            });
            $translateProvider.preferredLanguage('ja');
            $translateProvider.fallbackLanguage('en');
        }]);

</script>
<?php echo $this->Html->script('controller/team_member_list'); ?>

<div ng-app="myApp">
    <div class="col-xs-3">
        <ul class="nav" style="font-size: 13px;">
            <li class="active"><a ui-sref="member">チームメンバー一覧</a></li>
            <li class=""><a ui-sref="vision">チームビジョン一覧</a></li>
            <li class=""><a ui-sref="group_vision">グループビジョン一覧</a></li>
        </ul>
    </div>

    <div class="col-xs-9">
        <div ui-view> ロード中....</div>
    </div>
</div>
