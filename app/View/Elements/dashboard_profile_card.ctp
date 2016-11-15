<?php
/**
 * Created by PhpStorm.
 * User: kubotanaruhito
 * Date: 12/4/14
 * Time: 19:58
 *
 * @var CodeCompletionView $this
 * @var                    $action_count
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="dashboard-profile-card" xmlns="http://www.w3.org/1999/html">
    <div class="dashboard-profile-card-header">
        <div class="text-align_c mb_8px">
            <a class=""
               href="<?= $this->Html->url([
                   'controller' => 'users',
                   'action'     => 'view_goals',
                   'user_id'    => $this->Session->read('Auth.User.id')
               ]) ?>">
                <?= $this->Upload->uploadImage($my_prof, 'User.photo', ['style' => 'medium'],
                    ['class' => 'dashboard-profile-card-avatarImage inline-block']) ?>
            </a>
        </div>
        <div class="text-align_c">
            <a href="<?= $this->Html->url([
                'controller' => 'users',
                'action'     => 'view_goals',
                'user_id'    => $this->Session->read('Auth.User.id')
            ]) ?>">
                <span class="dashboard-profile-card-userField ln_1-f">
                    <?= h($this->Session->read('Auth.User.display_first_name')) ?>
                </span>
            </a>
        </div>
    </div>

    <div class="dashboard-profile-card-footer">
        <div class="text-align_c">
            <div class="mb_4px">
                <span class="dashboard-profile-card-score"
                      id="CountActionByMe"><?= $action_count ?></span>
            </div>
            <div class="ml_8px"><i
                    class="fa fa-check-circle mr_2px font_brownRed font_12px"></i><?= __("Action") ?>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>
