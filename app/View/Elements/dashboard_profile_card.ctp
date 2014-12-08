<?php
/**
 * Created by PhpStorm.
 * User: kubotanaruhito
 * Date: 12/4/14
 * Time: 19:58
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/dashboard_profile_card.ctp -->
<div class="dashboard-profile-card">
    <a class="dashboard-profile-card-bg col-xxs-12" tabindex="-1" href="/user"></a>
    <div class="dashboard-profile-card-content"></div>
    <a class="dashboard-profile-card-avator-link">
        <?= $this->Upload->uploadImage($this->Session->read('Auth.User'), 'User.photo', ['style' => 'medium'], ['class' => 'dashboard-profile-card-avatar-image']) ?>
    </a>
    <div class="dashboard-profile-card-user-field">
        <?= $this->Session->read('Auth.User.first_name') ?>
        <?= $this->Session->read('Auth.User.last_name') ?>
    </div>
    <div class="dashboard-profile-card-stats">
        <div class="dashboard-profile-card-point">
            <div>今期のポイント</div>
            <div>
                <span class="font_bold font_33px">1,246</span>pt
            </div>
            <div>
                <span>先週比</span>
                <span>(236↑)</span>
            </div>
        </div>
        <div class="dashboard-profile-card-activities">
            <div>今期のアクティビティ</div>
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
<!-- END app/View/Elements/dashboard_profile_card.ctp -->
