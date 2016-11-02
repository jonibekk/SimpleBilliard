<?php
/**
 * @var CodeCompletionView $this
 */
?>
<?= $this->App->viewStartComment() ?>
<style type="text/css">
    .team_member_table {
        background-color: #ffffff;
        font-size: 14px;
        border-radius: 5px;
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

<?php
echo $this->Html->script('vendor/angular/angular.min');
echo $this->Html->script('vendor/angular/angular-route.min');
echo $this->Html->script('vendor/angular/angular-translate.min');
echo $this->Html->script('vendor/angular/angular-translate-loader-static-files.min');
echo $this->Html->script('ng_controller');
?>

<div ng-app="myApp">
    <div ng-controller="TeamMemberMainController" ng-view
         class="col-md-6 col-xs-8 col-xxs-12 col-md-offset-3 col-xs-offset-2  layout-main"> ロード中....
    </div>
</div>
