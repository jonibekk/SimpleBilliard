<?
/**
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Teams/member_list.ctp -->
<style type="text/css">
    .vision_title {
        font-weight: bold;
    }

    .vision_modified_date {
        padding: 5px;
        font-size: 12px;
    }

    .vision_description {
        padding: 5px;
        font-size: 13px;
    }

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
                    url: "/vision/:team_id",
                    templateUrl: "/template/team_vision_list.html",
                    controller: 'TeamVisionController',
                    resolve: {
                        teamVisionList: ['$stateParams', '$http', function ($stateParams, $http) {
                            var request = {
                                method: 'GET',
                                url: cake.url.u + $stateParams.team_id
                            }
                            return $http(request).then(function (response) {
                                return response.data;
                            })

                        }]
                    }

                })
                .state('vision_archive', {
                    url: "/vision_archive/:team_id/:active_flg",
                    templateUrl: "/template/team_vision_list.html",
                    controller: 'TeamVisionArchiveController',
                    resolve: {
                        teamVisionArchiveList: ['$stateParams', '$http', function ($stateParams, $http) {
                            var request = {
                                method: 'GET',
                                url: cake.url.u + $stateParams.team_id + '/' + $stateParams.active_flg
                            }
                            return $http(request).then(function (response) {
                                return response.data;
                            })

                        }]
                    }

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
<?php echo $this->Html->script('controller/team_vision_list'); ?>

<div ng-app="myApp">
    <div class="col-xs-3">
        <ul class="nav" style="font-size: 13px;">
            <li class="active"><a ui-sref="member">チームメンバー一覧</a></li>
            <li class=""><a ui-sref="vision({team_id:1})">チームビジョン一覧</a></li>
            <li class=""><a ui-sref="group_vision">グループビジョン一覧</a></li>
        </ul>
    </div>

    <div class="col-xs-9">
        <div ui-view> ロード中....</div>
    </div>
</div>
