<?php
/**
 * Created by PhpStorm.
 * User: kubotanaruhito
 * Date: 12/4/14
 * Time: 19:58
 *
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/dashboard_profile_card.ctp -->
<div class="dashboard-profile-card" xmlns="http://www.w3.org/1999/html">
    <a class="dashboard-profile-card-bg col-xxs-12" tabindex="-1" href="/user"></a>

    <div class="dashboard-profile-card-content">
        <a class="dashboard-profile-card-avator-link">
            <?= $this->Upload->uploadImage($this->Session->read('Auth.User'), 'User.photo', ['style' => 'medium'],
                                           ['class' => 'dashboard-profile-card-avatar-image disp_ib']) ?>
        </a>

        <span class="dashboard-profile-card-user-field font_bold ln_1-f">
            <?= $this->Session->read('Auth.User.first_name') ?>
            <?= $this->Session->read('Auth.User.last_name') ?>
        </span>
        <div class="dashboard-profile-card-stats">
            <div class="dashboard-profile-card-point">
                <div class="ml_5px">今期のポイント</div>
                <div class="disp_ib">
                    <span class="dashboard-profile-card-score font_bold font_33px ml_5px">1,246</span>
                    <span class="ml_2px">pt</span>
                </div>
                <div class="disp_ib">
                    <div>先週比</div>
                    <span>(236↑)</span>
                </div>
            </div>
            <div class="dashboard-profile-card-activities bd-t mt_5px">
                <div class="ml_5px">今期のアクティビティ</div>
                <ul class="dashboard-profile-card-activity-list">
                    <li class="dashboard-profile-card-activity">
                        <span>アクション</span>
                        <span>50</span>
                    </li>
                    <li class="dashboard-profile-card-activity">
                        <span>出した成果</span>
                        <span>999,999</span>
                    </li>
                    <li class="dashboard-profile-card-activity">
                        <span>投稿</span>
                        <span>80</span>
                    </li>
                </ul>
                <div class="dashboard-profile-card-more-read">もっと見る</div>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/dashboard_profile_card.ctp -->
