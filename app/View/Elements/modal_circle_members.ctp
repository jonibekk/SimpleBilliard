<?php
/**
 * Created by PhpStorm.
 * User: t-tsunekawa
 * Date: 2015/04/3
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $circle_members
 * @var                    $circle_id
 */

// 管理者メンバー
$admin_circle_members = array_filter($circle_members, function ($v) {
    return $v['CircleMember']['admin_flg'];
});
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header no-border">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold"><?= __("Members in this circle.") ?></h4>
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#ModalCircleMemberTab1_<?= $circle_id ?>" data-toggle="tab"><?= __(
                        "Member(%s)",
                        count($circle_members)) ?></a>
            </li>
            <li><a href="#ModalCircleMemberTab2_<?= $circle_id ?>" data-toggle="tab"><?= __("Admin(%s)",
                        count($admin_circle_members)) ?></a>
            </li>
        </ul>
        <div class="modal-body modal-feed-body tab-content">
            <div class="tab-pane fade in active" id="ModalCircleMemberTab1_<?= $circle_id ?>">
                <?php if (!empty($circle_members)): ?>
                    <div class="row borderBottom">
                        <?php foreach ($circle_members as $user): ?>
                            <?=
                            $this->element('Feed/read_like_user',
                                [
                                    'user'     => $user['User'],
                                    'created'  => $user['CircleMember']['modified'],
                                    'is_admin' => $user['CircleMember']['admin_flg'],
                                    'type'     => 'rough'
                                ]) ?>
                        <?php endforeach ?>
                    </div>
                <?php else: ?>
                    <?= __("No one in this circle.") ?>
                <?php endif ?>
            </div>
            <div class="tab-pane fade in" id="ModalCircleMemberTab2_<?= $circle_id ?>">
                <div class="row borderBottom">
                    <?php foreach ($admin_circle_members as $user): ?>
                        <?=
                        $this->element('Feed/read_like_user',
                            [
                                'user'     => $user['User'],
                                'created'  => $user['CircleMember']['modified'],
                                'is_admin' => $user['CircleMember']['admin_flg'],
                                'type'     => 'rough'
                            ]) ?>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close") ?></button>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
