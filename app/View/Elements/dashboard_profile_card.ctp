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
<div class="dashboardProfileCard" xmlns="http://www.w3.org/1999/html">
    <a class="dashboardProfileCard-bg col-xxs-12" tabindex="-1" href="/#"></a>

    <div class="dashboardProfileCard-content">
        <a class="dashboardProfileCard-avatorLink">
            <?= $this->Upload->uploadImage($this->Session->read('Auth.User'), 'User.photo', ['style' => 'medium'],
                                           ['class' => 'dashboardProfileCard-avatarImage disp_ib']) ?>
        </a>

        <span class="dashboardProfileCard-userField font_bold font_verydark ln_1-f">
            <?= $this->Session->read('Auth.User.last_name') ?>
        </span>

        <div class="dashboardProfileCard-stats font_10px">
            <div class="dashboardProfileCard-point">
                <div class="ml_8px">今期のポイント</div>
                <div class="text-align_c">
                    <div class="disp_ib">
                        <span class="dashboardProfileCard-score font_bold font_33px ml_8px">1,246</span>
                        <span class="ml_2px">pt</span>
                    </div>
                    <div class="disp_ib">
                        <div class="ml_2px">先週比</div>
                        <span>
                            (<span class="font_seagreen font_bold plr_1px">236<i class="fa fa-level-up"></i></span>)
                        </span>
                    </div>
                </div>
            </div>
            <div class="dashboardProfileCard-activities bd-t mt_8px">
                <div class="ml_8px mt_5px">今期のアクティビティ</div>
                <ul class="dashboardProfileCard-activityList text-align_c col-xxs-12 p_8px mb_0px">
                    <li class="dashboardProfileCard-activity disp_ib col-xxs-4">
                        <div class="ls_title">アクション</div>
                        <i class="fa fa-check-circle mr_1px"></i><span class="ls_number">50</span>
                    </li>
                    <li class="dashboardProfileCard-activity disp_ib col-xxs-4">
                        <div class="ls_title">成果</div>
                        <i class="fa fa-key mr_1px"></i><span class="ls_number">99,999</span>
                    </li>
                    <li class="dashboardProfileCard-activity disp_ib col-xxs-4">
                        <div class="ls_title">投稿</div>
                        <i class="fa fa-comment-o mr_1px"></i><span class="ls_number">80</span>
                    </li>
                </ul>
                <div class="dashboardProfileCard-moreRead text-align_c mtb_8px"><a class="font_lightGray-gray"
                                                                                      href="/#"><i
                            class="fa fa-eye mr_5px"></i>もっと見る</a></div>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/dashboard_profile_card.ctp -->
