<?php
/**
 * @var $kr
 * @var $incomplete_kr_count
 * @var $without_dropdown_link
 */
if (!isset($without_dropdown_link)) {
    $without_dropdown_link = false;
}
if (!isset($incomplete_kr_count)) {
    $incomplete_kr_count = 0;
}
$is_tkr = null;
?>
<?php if (isset($kr) && $kr): ?>
    <?php if (isset($kr['KeyResult'])) {
        $kr = $kr['KeyResult'];
        $is_tkr = Hash::get($kr,'tkr_flg');
    } ?>
    <?= $this->App->viewStartComment() ?>
    <?php if (!$without_dropdown_link): ?>
        <div class="btn-edit-kr-wrap pull-right dropdown">
        <a href="#" class="font_lightGray-gray font_14px plr_4px pt_2px pb_2px"
           data-toggle="dropdown"
           id="download">
            <i class="fa fa-ellipsis-h btn-edit-kr"></i>
        </a>
    <?php endif; ?>
    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
        aria-labelledby="dropdownMenu1">
        <?php if (!$kr['completed']): ?>
            <li role="presentation">
                <?php
                //TKRの場合はゴール修正ぺージのリンク
                if ($is_tkr) {
                    $url = "/goals/" . $kr['goal_id'] . "/edit";
                } else {
                    $url = [
                        'controller'    => 'goals',
                        'action'        => 'ajax_get_edit_key_result_modal',
                        'key_result_id' => $kr['id']
                    ];
                }
                ?>
                <a href="<?= $this->Html->url($url) ?>"
                   class="<?= !$is_tkr ? "modal-ajax-get-add-key-result" : null //このクラスがある場合はKR編集モーダル  ?>">
                    <i class="fa fa-pencil"></i><span class="ml_2px"><?= __("Edit Key Result") ?></span></a>
            </li>
        <?php endif ?>
        <li role="presentation">
            <?php if ($kr['completed']): ?>
                <?= $this->Form->postLink('<i class="fa fa-reply"></i><span class="ml_2px">' .
                    __("Uncompete Key Result") . '</span>',
                    ['controller' => 'goals', 'action' => 'incomplete_kr', 'key_result_id' => $kr['id']],
                    ['escape' => false]) ?>
            <?php else: ?>
                <?php //最後のKRの場合
                if ($incomplete_kr_count === 1):?>
                    <a href="<?= $this->Html->url([
                        'controller'    => 'goals',
                        'action'        => 'ajax_get_last_kr_confirm',
                        'key_result_id' => $kr['id']
                    ]) ?>"
                       class="modal-ajax-get">
                        <i class="fa fa-check"></i><span class="ml_2px"><?= __(
                                "Complete Key Result") ?></span>
                    </a>
                <?php else: ?>
                    <?=
                    $this->Form->create('Goal', [
                        'url'           => [
                            'controller'    => 'goals',
                            'action'        => 'complete_kr',
                            'key_result_id' => $kr['id']
                        ],
                        'inputDefaults' => [
                            'div'       => 'form-group',
                            'label'     => false,
                            'wrapInput' => '',
                        ],
                        'class'         => 'form-feed-notify',
                        'name'          => 'kr_achieve_' . $kr['id'],
                        'id'            => 'kr_achieve_' . $kr['id']
                    ]); ?>
                    <?php $this->Form->unlockField('socket_id') ?>
                    <?= $this->Form->end() ?>
                    <a href="#" form-id="kr_achieve_<?= $kr['id'] ?>"
                       class="kr_achieve_button">
                        <i class="fa fa-check"></i><span class="ml_2px">
                                            <?= __("Complete Key Result"); ?>
                                        </span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </li>

        <?php if (!$is_tkr && !$kr['completed']): ?>
            <li role="presentation">
                <?=
                $this->Form->postLink('<i class="fa fa-trash"></i><span class="ml_5px">' .
                    __("Delete Key Result") . '</span>',
                    ['controller' => 'goals', 'action' => 'delete_key_result', 'key_result_id' => $kr['id']],
                    ['escape' => false], __("Do you really want to delete this Key Result?")) ?>
            </li>
        <?php endif ?>
    </ul>
    <?php if (!$without_dropdown_link): ?>
        </div>
    <?php endif; ?>

    <?= $this->App->viewEndComment() ?>
<?php endif ?>
