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
                <button type="button" class="close font_33px close-design modal-close-wrap" data-dismiss="modal"
                        aria-hidden="true">
                    <i class="fa fa-close modal-close-icon"></i>
                </button>
                <a href="#" id="modalTutorialPrev" class="modalTutorial-btnPrev no-line">
                    <i class="fa fa-angle-left font_72px modalTutorial-btnIcon"></i>
                </a>
                <a href="#" id="modalTutorialNext" class="modalTutorial-btnNext no-line">
                    <i class="fa fa-angle-right font_72px modalTutorial-btnIcon"></i>
                </a>
                <a href="#" id="modalTutorialGo" class="modalTutorial-btnGo no-line" data-dismiss="modal">
                    <span class="font_33px modalTutorial-btnIcon">Go</span>
                </a>
            </div>
            <div class="modal-footer">
                <div class="col-xxs-12 text-align_l tutorial-texts">
                    <p id="tutorialText1" class="tutorial-text">
                        <?= __("You can check who read your posts and get Like from your team members.") ?>
                    </p>

                    <p id="tutorialText2" class="tutorial-text">
                        <?= __("Make circle, join circle. There are 2 type of circles. Secret and Public.") ?>
                    </p>

                    <p id="tutorialText3" class="tutorial-text">
                        <?= __("Make a goal. You can set it in details at goal page.") ?>
                    </p>

                    <p id="tutorialText4" class="tutorial-text">
                        <?= __("Make your team better by Goalous.") ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_tutorial.ctp -->
