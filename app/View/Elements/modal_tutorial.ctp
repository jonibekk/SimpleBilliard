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
        <div class="modal-content gl-modal-no-margin">
            <div class="modal-header">
                <button type="button" class="close font-size_33 close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
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
                            <?= $this->Html->image('homepage/home-slider/slide1.jpg') ?>
                        </div>
                        <div class="item">
                            <?= $this->Html->image('homepage/home-slider/slide2.jpg') ?>
                        </div>
                        <div class="item">
                            <?= $this->Html->image('homepage/home-slider/slide3.jpg') ?>
                        </div>
                        <div class="item">
                            <?= $this->Html->image('homepage/home-slider/slide4.jpg') ?>
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
<!-- END app/View/Elements/modal_tutorial.ctp -->
