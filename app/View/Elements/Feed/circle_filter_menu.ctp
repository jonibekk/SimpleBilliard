<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 8/3/15
 * Time: 11:58 AM
 *
 * @var $current_circle
 * @var $user_status
 * @var $circle_member_count
 * @var $circle_status
 */
?>
<?= $this->App->viewStartComment()?>
<span id="circle-filter-menu-circle-name"
      class="feed-current-filter"><?= mb_strimwidth(h($current_circle['Circle']['name']), 0, 29,
        '...') ?></span>
<a href="<?= $this->Html->url([
    'controller' => 'circles',
    'action'     => 'ajax_get_circle_members',
    'circle_id'  => $current_circle['Circle']['id']
]) ?>"
     class="modal-ajax-get remove-on-hide" id="circle-filter-menu-member-url">
     <span class="feed-circle-user-number"><i class="fa fa-user"></i>&nbsp;
        <span id="circle-filter-menu-circle-member-count">
            <?php if (isset($circle_member_count)): ?><?= $circle_member_count ?><?php endif ?>
        </span>
    </span>
</a>
<div class="pull-right header-function dropdown" id="CircleFilterMenuDropDown">
    <a id="download" data-toggle="dropdown"
       class="font_lightGray-gray"
       href="#" style="opacity: 0.54;">
        <i class="fa fa-cog header-function-icon"
           style="color: rgb(80, 80, 80); opacity: 0.88;"></i>
        <i class="fa fa-caret-down goals-column-fa-caret-down header-function-icon"
           style="color: rgb(80, 80, 80); opacity: 0.88;"></i>
    </a>
    <ul aria-labelledby="dropdownMenu1" role="menu"
        class="dropdown-menu dropdown-menu-right frame-arrow-icon">
        <?php if (isset($user_status)): ?>
            <?php if (!$current_circle['Circle']['team_all_flg'] && $user_status != 'admin'): ?>
                <li>
                    <?php if ($user_status != 'joined') { ?>
                        <a href="<?= $this->Html->url([
                            'controller' => 'posts',
                            'action'     => 'join_circle',
                            'circle_id'  => $current_circle['Circle']['id']
                        ]) ?>">
                            <?= __('Join circle') ?></a>
                    <?php } else { ?>
                        <a href="<?= $this->Html->url([
                            'controller' => 'posts',
                            'action'     => 'unjoin_circle',
                            'circle_id'  => $current_circle['Circle']['id']
                        ]) ?>">
                            <?= __('Leave circle.') ?></a>
                    <?php } ?>
                </li>
            <?php endif; ?>
            <?php if (($user_status == 'joined' || $user_status == 'admin') && ENV_NAME != 'isao'): ?>
                <li><a href="<?= $this->Html->url([
                        'controller' => 'circles',
                        'action'     => 'ajax_setting',
                        'circle_id'  => $current_circle['Circle']['id']
                    ]) ?>" class="modal-circle-setting"><?= __('Settings') ?></a></li>
            <?php endif ?>
        <?php endif ?>
    </ul>
</div>
<?= $this->App->viewEndComment()?>
