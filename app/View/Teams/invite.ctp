<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/30/14
 * Time: 9:59 AM
 *
 * @var CodeCompletionView $this
 * @var array              $my_teams
 */
?>
<!-- START app/View/Teams/invite.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("Send Invitations") ?></div>
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
                <?=
                $this->Form->input('emails', [
                    'label'                        => __("Email address"),
                    'type'                         => 'text',
                    'rows'                         => 3,
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 2000,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 2000),
                    "data-bv-notempty-message"     => __("Input is required."),
                ]) ?>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->button(__('Next') . ' <i class="fa fa-angle-right"></i>',
                            [
                                'type'     => 'submit',
                                'class'    => 'btn btn-primary',
                                'disabled' => 'disabled',
                                'escape'   => false
                            ]) ?>
                        <?php if (isset($from_setting) && !$from_setting): ?>
                            <?=
                            $this->Html->link(__("Skip for Now"), "/",
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
    </div>
</div>
<!-- END app/View/Teams/invite.ctp -->
