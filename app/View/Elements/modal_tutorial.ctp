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
<div class="modal fade" id="modal_tutorial">
    <div class="modal-dialog">
        <div class="modal-content gl-modal-no-margin">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= __d('gl', "チュートリアル") ?></h4>
            </div>
            <div class="modal-body">
                <div id="carousel-tutorial" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-tutorial" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-tutorial" data-slide-to="1"></li>
                        <li data-target="#carousel-tutorial" data-slide-to="2"></li>
                        <li data-target="#carousel-tutorial" data-slide-to="3"></li>
                    </ol>
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner">
                        <div class="item active">
                            <?= $this->Html->image('homepage/home-slider/mv1.png') ?>
                            <div class="carousel-caption">
                                <?= __d('gl', "ゴール") ?>
                            </div>
                        </div>
                        <div class="item">
                            <?= $this->Html->image('homepage/home-slider/mv2.png') ?>
                            <div class="carousel-caption">
                                <?= __d('gl', "オープン") ?>
                            </div>
                        </div>
                        <div class="item">
                            <?= $this->Html->image('homepage/home-slider/mv3.png') ?>
                            <div class="carousel-caption">
                                <?= __d('gl', "コラボレーション") ?>
                            </div>
                        </div>
                        <div class="item">
                            <?= $this->Html->image('homepage/home-slider/mv4.png') ?>
                            <div class="carousel-caption">
                                <?= __d('gl', "Goalous") ?>
                            </div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <a class="left carousel-control" href="#carousel-tutorial" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href="#carousel-tutorial" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
            </div>
        </div>
    </div>
</div>