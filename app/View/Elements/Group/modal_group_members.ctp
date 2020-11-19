<?php?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold">
                <?= __("Active Members") ?>(<?= count($groupMembers) ?>)
            </h4>
        </div>
        <div class="modal-body without-footer">
            <?php if (!empty($groupMembers)): ?>
                <div class="row borderBottom">
                    <?php foreach ($groupMembers as $user): ?>
                        <div class="col col-xxs-12 mpTB0">
                            <?=
                            $this->Upload->uploadImage($user['User'], 'User.photo', ['style' => 'medium_large'],
                                ['class' => 'comment-img'])
                            ?>
                            <div class="comment-body modal-comment">
                                <div class="font_12px font_bold modalFeedTextPadding">
                                    <?= h($user['User']['display_username']) ?>&nbsp;
                                    <?php if (viaIsSet($is_admin)): ?>
                                        <i class="fa fa-adn team-members-card-admin-icon"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <?= __("There is no like! yet.") ?>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
