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
<?= $this->App->viewStartComment()?>
<div class="dashboardProfileCard" xmlns="http://www.w3.org/1999/html">
    <a class="dashboardProfileCard-bg col-xxs-12" tabindex="-1" href="/#"></a>

    <div class="dashboardProfileCard-content">
        <a class="dashboardProfileCard-avatarLink"
           href="<?= $this->Html->url([
               'controller' => 'users',
               'action'     => 'view_goals',
               'user_id'    => $this->Session->read('Auth.User.id')
           ]) ?>">
            <?= $this->Upload->uploadImage($my_prof, 'User.photo', ['style' => 'medium'],
                ['class' => 'dashboardProfileCard-avatarImage inline-block']) ?>
        </a>
        <a href="<?= $this->Html->url([
            'controller' => 'users',
            'action'     => 'view_goals',
            'user_id'    => $this->Session->read('Auth.User.id')
        ]) ?>">
        <span class="dashboardProfileCard-userField font_bold font_verydark ln_1-f">
            <?= h($this->Session->read('Auth.User.display_first_name')) ?>
        </span>
        </a>

        <div class="dashboardProfileCard-stats font_10px">
            <div class="dashboardProfileCard-point">
                <div class="text-align_c">
                    <div class="inline-block">
                        <span class="dashboardProfileCard-score font_bold font_33px ml_8px"
                              id="CountActionByMe"><?= $action_count ?></span>
                    </div>
                    <div class="ml_8px mt_6px"><i
                            class="fa fa-check-circle mr_2px font_brownRed font_12px"></i><?= __("Action") ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
