<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="modal fade" tabindex="-1" id="modal_tutorial">
    <div class="modal-dialog modal-tutorial-dialog">
        <div class="modal-content parent-p_0px">
            <div class="modal-body modal-close-base tutorial-body">
                <div class="tutorial-box1 col-xxs-12">
                    <img src="/img/pre-load.svg" class="modal-tutorial-image lazy"
                         data-original="/img/tutorial/slide01.png">
                </div>
                <div class="tutorial-box2 col-xxs-12 none">
                    <img src="/img/pre-load.svg" class="modal-tutorial-image lazy"
                         data-original="/img/tutorial/slide02.png">
                </div>
                <div class="tutorial-box3 col-xxs-12 none">
                    <img src="/img/pre-load.svg" class="modal-tutorial-image lazy"
                         data-original="/img/tutorial/slide03.png">
                </div>
            </div>
            <div class="modal-footer setup-tutorial-modal-footer">
                <div class="col-xxs-12 text-align_l setup-tutorial-texts">
                    <div class="tutorial-text1">
                        <div class="setup-tutorial-text-title"><?= __("Create a goal.") ?></div>
                        <?= __("Make a good Goal to discuss with your project members.") ?>
                    </div>

                    <div class="tutorial-text2 none">
                        <div class="setup-tutorial-text-title"><?= __("Have a common Goal") ?></div>
                        <?= __("Collaborate with achieving the Goal.") ?>
                    </div>

                    <div class="tutorial-text3 none">
                        <div class="setup-tutorial-text-title"><?= __("Action for your Goal") ?></div>
                        <?= __("Let's action to show your activity.") ?>
                    </div>
                    <p class="tutorial-text4 none">
                        <?= __("Make your team better by Goalous.") ?>
                    </p>
                </div>
                <div class="setup-tutorial-navigation">
                    <span class="setup-tutorial-navigation-skip" data-dismiss="modal" aria-hidden="true">
                        SKIP
                    </span>
                    <span class="setup-tutorial-navigation-indicator">
                        <span
                            class="setup-tutorial-indicator setup-tutorial-indicator1 setup-tutorial-navigation-indicator-selected"
                            data-id="1">
                        ●
                        </span>
                        <span class="setup-tutorial-indicator setup-tutorial-indicator2" data-id="2">
                        ●
                        </span>
                        <span class="setup-tutorial-indicator setup-tutorial-indicator3" data-id="3">
                        ●
                        </span>
                    </span>
                    <span class="tutorial-next-btn">
                        <i class="fa fa-arrow-circle-right tutorial-next-arrow"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>
