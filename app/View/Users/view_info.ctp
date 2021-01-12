<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var $user
 */
?>
<?= $this->App->viewStartComment()?>
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('User/simplex_top_section') ?>
        <div class="panel-body view-info-panel">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#eee; font-size:100%">
                    <?= __("Profile") ?>
                    <?php if ($this->Session->read('Auth.User.id') == $user['User']['id']): ?>
                        <span class="pull-right" style="font-weight:normal;">
                            <i class="fa fa-pencil"></i>
                            <?= $this->Html->link(__('Edit'), [
                                'controller' => 'users',
                                'action'     => 'settings',
                                '#'          => 'profile'
                            ]) ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="panel-body">
                    <?= __('Name') ?>
                    <span class="pull-right"><?= h($user['User']['display_username']) ?></span>
                </div>
                <div class="panel-body" style="border-top: 1px solid #ddd;">
                    <?= __('Member ID') ?>
                    <span class="pull-right"><?= h($user['TeamMember']['member_no']) ?></span>
                </div>
                <div class="panel-body" style="border-top: 1px solid #ddd;">
                    <?= __('Gender') ?>
                    <span
                        class="pull-right"><?= $user['User']['gender_type'] ? User::$TYPE_GENDER[$user['User']['gender_type']] : null ?></span>
                </div>
                <div class="panel-body" style="border-top: 1px solid #ddd;">
                    <?= __('Birthday') ?>
                    <span class="pull-right"><?= $this->Time->format($user['User']['hide_year_flg'] ? 'm/d' : 'Y/m/d',
                            $user['User']['birth_day']) ?></span>
                </div>
                <div class="panel-body" style="border-top: 1px solid #ddd;">
                    <?= __('Birthplace') ?>
                    <span class="pull-right"><?= h($user['User']['hometown']) ?></span>
                </div>
                <div class="panel-body" style="border-top: 1px solid #ddd;">
                    <?= __('Profile Image') ?>
                    <span class="pull-right"><?=
                        $this->Upload->uploadImage($user, 'User.photo',
                            ['style' => 'small']) ?></span>
                </div>
                <div class="panel-body" style="border-top: 1px solid #ddd;">
                    <?= __('Groups') ?>
                    <span class="pull-right">
                        <ul>
                            <?php foreach ($groups as $group) : ?>
                                <li><?= $group["name"] ?></li>
                            <?php endforeach ?>
                        </ul>
                        <?php if (!empty($archivedGroups)) : ?>
                            <p>
                                <a href="#" class="archived-toggle"><?= __('View archived groups') ?></a>
                            </p>
                            <ul class="archived-list">
                                <?php foreach ($archivedGroups as $group) : ?>
                                    <li><?= $group["name"] ?></li>
                                <?php endforeach ?>
                            </ul>
                        <?php endif ?>
                    </span>
            </div>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
<?php $this->append('script') ?>
    <script type="text/javascript">
        $(document).ready(function () {
            var archivedHidden = true;
            var archivedToggle = $('.archived-toggle');
            var archivedList = $('.archived-list');

            archivedToggle.click(function(e) {
                e.preventDefault();
                

                if (archivedHidden) {
                    archivedToggle.text("<?= __("Hide archived groups") ?>");
                    archivedList.css("display", "block");
                } else {
                    archivedToggle.text("<?= __("View archived groups") ?>");
                    archivedList.css("display", "none");
                }

                archivedHidden = !archivedHidden
            })
        });
    </script>
<?php $this->end() ?>
