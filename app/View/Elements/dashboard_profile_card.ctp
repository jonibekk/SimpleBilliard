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

        <span class="dashboard-profile-card-user-field font_bold font_verydark ln_1-f">
            <?= $this->Session->read('Auth.User.last_name') ?>
        </span>
        <div class="dashboard-profile-card-stats">
            <div class="dashboard-profile-card-point">
                <div class="ml_8px">今期のポイント</div>
                <div class="disp_ib">
                    <span class="dashboard-profile-card-score font_bold font_33px ml_8px">1,246</span>
                    <span class="ml_2px">pt</span>
                </div>
                <div class="disp_ib">
                    <div class="font_11px ml_2px">先週比</div>
                    <span>(<span class="font_seagreen font_bold plr_1px">236<i class="fa fa-level-up"></i></span>)</span>
                </div>
            </div>
            <div class="dashboard-profile-card-activities bd-t mt_8px">
                <div class="ml_8px mt_5px">今期のアクティビティ</div>
                <ul class="dashboard-profile-card-activity-list text-align_c">
                    <li class="dashboard-profile-card-activity disp_ib font_11px col-xxs-4">
                        <div>アクション</div>
                        <i class="fa fa-check-circle"></i><span>50</span>
                    </li>
                    <li class="dashboard-profile-card-activity disp_ib font_11px col-xxs-4">
                        <div>出した成果</div>
                        <i class="fa fa-key"></i><span>999,999</span>
                    </li>
                    <li class="dashboard-profile-card-activity disp_ib font_11px col-xxs-4">
                        <div>投稿</div>
                        <i class="fa fa-comment-o"></i><span>80</span>
                    </li>
                </ul>
                <div class="dashboard-profile-card-more-read text-align_c mtb_8px"><a class="font_lightGray-gray" href="/#"><i class="fa fa-eye mr_5px"></i>もっと見る</a></div>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/dashboard_profile_card.ctp -->
