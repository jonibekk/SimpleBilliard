<!-- START app/View/Teams/member_list.ctp -->

<?php echo $this->Html->css('team_page'); ?>
<?php echo $this->Html->script('app/team'); ?>
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
