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
<!-- START app/View/Elements/modal_tutorial.ctp -->
<div class="modal fade" tabindex="-1" id="modal_tutorial">
    <div class="modal-dialog">
        <div class="modal-content parent-p_0px">
            <div class="modal-body modal-close-base tutorial-body">
                <div id="modalTutorialBox" class="tutorial-box1 col-xxs-12">
                </div>
            </div>
            <div class="modal-footer setup-tutorial-modal-footer">
                <div class="col-xxs-12 text-align_l setup-tutorial-texts">
                    <div id="tutorialText1" class="tutorial-text">
                        <div class="setup-tutorial-text-title"><?= __("Create a goal.") ?></div>
                        <?= __("The goal that should be accomplished.") ?>
                    </div>

                    <div id="tutorialText2" class="tutorial-text">
                        <div class="setup-tutorial-text-title"><?= __("Circle Conversations") ?></div>
                        <?= __("If someone is added to a group conversation.") ?>
                    </div>

                    <div id="tutorialText3" class="tutorial-text">
                        <div class="setup-tutorial-text-title"><?= __("Messenger") ?></div>
                        <?= __("Messenger is jus lie texting, but you don't have to pay for every message.") ?>
                    </div>

                    <p id="tutorialText4" class="tutorial-text">
                        <?= __("Make your team better by Goalous.") ?>
                    </p>
                </div>
                <div class="setup-tutorial-navigation" >
                    <span class="setup-tutorial-navigation-skip" data-dismiss="modal" aria-hidden="true">
                        SKIP
                    </span>
                    <span class="setup-tutorial-navigation-indicator">
                        <span id="tutorialIndicator1">
                        ●
                        </span>
                        <span id="tutorialIndicator2">
                        ●
                        </span>
                        <span id="tutorialIndicator3">
                        ●
                        </span>
                    </span>
                    <span class="setup-tutorial-navigation-next" id="modalTutorialNext">
                        →
                    </span>
                    <span class="setup-tutorial-navigation-next" id="modalTutorialFinish" data-dismiss="modal" aria-hidden="true">
                        →
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_tutorial.ctp -->
