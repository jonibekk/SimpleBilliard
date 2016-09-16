<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/30/14
 * Time: 9:59 AM
 *
 * @var CodeCompletionView $this
 * @var array              $team
 * @var                    $from_setting
 */
?>
<?= $this->App->viewStartComment()?>
<div class="panel panel-default">
    <div class="panel-heading"><?= __("Invite members") ?></div>
    <?=
    $this->Form->create('Team', [
        'inputDefaults' => [
            'div'       => 'form-group',
            'label'     => [
                'class' => 'col col-sm-3 control-label form-label'
            ],
            'wrapInput' => 'col col-sm-6',
            'class'     => 'form-control'
        ],
        'class'         => 'form-horizontal',
        'novalidate'    => true,
        'id'            => 'InviteTeamForm',
        'url'           => ['action' => 'invite'],
        'method'        => 'post'
    ]); ?>
    <div class="panel-body">
        <div class="form-group">
            <label for="TeamName" class="col col-sm-3 control-label form-label"><?= __("Team Name") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static"><?= h($team['Team']['name']) ?></p>
            </div>
        </div>
        <hr>
        <?=
        $this->Form->input('emails', [
            'label'                        => __("Email address"),
            'type'                         => 'text',
            'rows'                         => 3,
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 2000,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 2000),
            "data-bv-notempty-message"     => __("Input is required."),
            'afterInput'                   => '<span class="help-block">'
                . '<p class="font_11px">' . __("You can set email addresses by comma(,) separated or by newline separated.") . '</p>'
                . '<ul class="example-indent font_11px"><li>' . __("eg. %s",
                    1) . ' aaa@example.com,bbb@example.com</li></ul>'
                . '<ul class="example-indent font_11px"><li>'
                . '' . __("eg. %s", 2) . ' aaa@example.com</br>'
                . 'aaa@example.com</br>'
                . '</li></ul>'
                . '</span>'
        ]) ?>
        <hr>
        <?=
        $this->Form->input('comment', [
            'label'                        => __("Comment(optional)"),
            'type'                         => 'text',
            'rows'                         => 3,
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 2000,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 2000),
            'afterInput'                   => '<span class="help-block font_11px">' . __(
                    "Comment will be added to the body of the invitation email.") . '</span>'
        ]) ?>
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="col-sm-9 col-sm-offset-3">
                <?=
                $this->Form->submit(__("Send an invitation email"),
                    ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>
                <?php if (isset($from_setting) && !$from_setting): ?>
                    <?=
                    $this->Html->link(__("Skip"), "/",
                        ['class' => 'btn btn-default', 'div' => false]) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?= $this->Form->end(); ?>
</div>
<?php $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {

        $('[rel="tooltip"]').tooltip();

        $('#InviteTeamForm').bootstrapValidator({
            live: 'enabled'
        });
    });
</script>
<?php $this->end() ?>
<?= $this->App->viewEndComment()?>
