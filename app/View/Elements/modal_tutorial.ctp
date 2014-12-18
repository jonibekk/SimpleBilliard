<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var View $this
 */
?>
<!-- START app/View/Elements/modal_tutorial.ctp -->
<div class="modal fade" tabindex="-1" id="modal_tutorial">
    <div class="modal-dialog">
        <div class="modal-content parent-p_0px">
            <div class="modal-body modal-close-base tutorial-body">
                <div class="tutorial-box col-xxs-12">

<!--
                    <?= $this->Html->image('homepage/home-slider/slide1.jpg', array('alt'=>'News Feedに投稿してみよう', 'width'=>'800', 'height'=>'600', 'class'=>'slide-pic', 'id'=>'slide01')) ?>
                    <?= $this->Html->image('homepage/home-slider/slide4.jpg', array('alt'=>'News Feedに投稿してみよう', 'width'=>'800', 'height'=>'600', 'class'=>'slide-pic', 'id'=>'slide04')) ?>
                    <?= $this->Html->image('homepage/home-slider/slide3.jpg', array('alt'=>'News Feedに投稿してみよう', 'width'=>'800', 'height'=>'600', 'class'=>'slide-pic', 'id'=>'slide03')) ?>
                    <?= $this->Html->image('homepage/home-slider/slide2.jpg', array('alt'=>'News Feedに投稿してみよう', 'width'=>'800', 'height'=>'600', 'class'=>'slide-pic', 'id'=>'slide02')) ?>
-->

                </div>
                <button type="button" class="close font_33px close-design modal-close-wrap" data-dismiss="modal"
                        aria-hidden="true">
                    <i class="fa fa-close modal-close-icon"></i>
                </button>
            </div>
            <div class="modal-footer">
                <div class="col-xxs-12 text-align_l tutorial-text">
                    <?= __d('gl', "ニュースフィードに投稿してみよう。既読管理ができて、いいね！が伝わります。") ?>
                    <?= __d('gl', "サークルをつくったり、サークルに入ってみよう。サークルには秘密と公開の2種類があります。") ?>
                    <?= __d('gl', "ゴールをつくりましょう。ゴールページでは、より詳細な設定ができます。") ?>
                    <?= __d('gl', "チーム力を向上させよう。Goalousをさっそく使ってみましょう") ?>
                </div>
                <div class="col-xxs-12">
                    <!--
                    <button class="btn-danger pull-left"><?= __d('gl', "閉じる") ?></button>
                    -->
                    <a href="#carousel-tutorial" data-slide="prev">
                        <button class="btn-default pull-left"><?= __d('gl', '戻る') ?></button>
                    </a>
                    <a href="#carousel-tutorial" data-slide="next">
                        <button class="btn-default"><?= __d('gl', '次へ') ?></button>
                    </a>
                </div>
                <!--
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                </button>
-->
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_tutorial.ctp -->
