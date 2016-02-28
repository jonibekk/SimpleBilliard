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
<!-- START app/View/Users/view_info.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('User/simplex_top_section') ?>
        <div class="panel-body view-info-panel">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#eee; font-size:100%">
                    <?= __("プロフィール") ?>
                    <?php if ($this->Session->read('Auth.User.id') == $user['User']['id']): ?>
                        <span class="pull-right" style="font-weight:normal;">
                            <i class="fa fa-pencil"></i>
                            <?= $this->Html->link(__('編集'), [
                                'controller' => 'users',
                                'action'     => 'settings',
                                '#'          => 'profile']) ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="panel-body">
                    <?= __('名前') ?>
                    <span class="pull-right"><?= h($user['User']['display_username']) ?></span>
                </div>
                <div class="panel-body" style="border-top: 1px solid #ddd;">
                    <?= __('Gender') ?>
                    <span class="pull-right"><?= User::$TYPE_GENDER[$user['User']['gender_type']] ?></span>
                </div>
                <div class="panel-body" style="border-top: 1px solid #ddd;">
                    <?= __('Birthday') ?>
                    <span class="pull-right"><?= $this->Time->format($user['User']['hide_year_flg'] ? 'm/d' : 'Y/m/d',
                                                                     $user['User']['birth_day']) ?></span>
                </div>
                <div class="panel-body" style="border-top: 1px solid #ddd;">
                    <?= __('出身地') ?>
                    <span class="pull-right"><?= h($user['User']['hometown']) ?></span>
                </div>
                <div class="panel-body" style="border-top: 1px solid #ddd;">
                    <?= __('プロフィール画像') ?>
                    <span class="pull-right"><?=
                        $this->Upload->uploadImage($user, 'User.photo',
                                                   ['style' => 'small']) ?></span>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- END app/View/Users/view_info.ctp -->
