<!-- START app/View/Teams/member_list.ctp -->

<?php echo $this->Html->css('team_page'); ?>
<?php echo $this->Html->script('ng_app.min'); ?>
<?php echo $this->Html->script('ng_controller'); ?>

<div ng-app="myApp">
    <div class="col-xs-3">
        <?= $this->element('Team/side_menu', ['angular' => true]) ?>
    </div>

    <div class="col-xs-9">
        <div ui-view> ロード中....</div>
    </div>
</div>
