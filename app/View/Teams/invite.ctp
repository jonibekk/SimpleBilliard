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
                <div class="form-group">
                    <?= __('good. Know your coworker.') ?>
                </div>
                <div class="form-group">
                    <label for="TeamEmails"
                           class="col col-xxs-8 col-sm-3 control-label form-label"><?= __("Email address") ?></label>
                    <div class="col col-xxs-4 col-sm-6">
                        <a href="#" class="form-control-static pull-right"><i
                                class="fa fa-plus"></i> <?= __('Add email') ?></a>
                    </div>
                </div>
                <?=
                $this->Form->input('Team.0.email', [
                    'label'                        => "",
                    'type'                         => 'string',
                    'placeholder'                  => 'you@yourdomain.com',
                    "data-bv-notempty"             => "false",
                    'data-bv-emailaddress'         => "false",
                    "data-bv-callback"             => "true",
                    "data-bv-callback-message"     => " ",
                    "data-bv-callback-callback"    => "bvCallbackAvailableEmailCanInvite",
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 200,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                    'required'                     => false,
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

                // 登録可能な email の validate
                require(['validate'], function (validate) {
                    window.bvCallbackAvailableEmailCanInvite = validate.bvCallbackAvailableEmailCanInvite;
                });


                $('#InviteTeamForm').bootstrapValidator({
                    live: 'enabled'
                });
            });
        </script>
        <?php $this->end() ?>
    </div>
</div>
<!-- END app/View/Teams/invite.ctp -->
