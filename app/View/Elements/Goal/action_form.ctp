<?php
// 編集時に true
$is_edit_mode = isset($common_form_mode) && $common_form_mode == 'edit';
?>
<?= $this->App->viewStartComment() ?>
<div id="ActionFormWrapper">
    <div class="panel panel-default global-form" id="GlobalForms">
        <div class="post-panel-heading ptb_7px plr_11px">
            <!-- Nav tabs -->
            <ul class="feed-switch clearfix plr_0px" role="tablist" id="CommonFormTabs">
                <li class="switch-action">
                    <a href="#ActionForm" role="tab" data-toggle="tab"
                       class="switch-action-anchor click-target-focus"
                       target-id="CommonActionName"><i
                            class="fa fa-check-circle"></i><?= __("Action") ?></a><span class="switch-arrow"></span>
                </li>
            </ul>
        </div>
        <!-- Tab panes -->
        <div class="tab-content">
            <?= $this->element('Goal/action_form_content', compact('is_edit_mode')) ?>
        </div>
    </div>

    <?= $this->element('file_upload_form') ?>
</div>
<?= $this->App->viewEndComment() ?>


