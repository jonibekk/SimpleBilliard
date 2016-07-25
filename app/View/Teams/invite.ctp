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
                    'div'       => [
                        'class' => 'form-group',
                    ],
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
                <?php $default_email_count = 3 ?>
                <div class="form-group">
                    <label for="TeamEmails"
                           class="col col-xxs-8 col-sm-3 control-label form-label"><?= __("Email address") ?></label>
                    <div class="col col-xxs-4 col-sm-6">
                        <a href="#" class="form-control-static pull-right" id="AddEmail"
                           index="<?= $default_email_count ?>" max_index="100"><i
                                class="fa fa-plus"></i> <?= __('Add email') ?></a>
                    </div>
                </div>
                <?php for ($i = 0; $i < $default_email_count; $i++): ?>
                    <?=
                    $this->Form->input("Team.emails.$i", [
                        'label'                        => "",
                        'type'                         => 'string',
                        'placeholder'                  => 'name@domain.com',
                        "data-bv-notempty"             => $i == 0 ? "true" : "false",//１行目のみ必須
                        'data-bv-emailaddress'         => "false",
                        "data-bv-callback"             => "true",
                        "data-bv-callback-message"     => " ",
                        "data-bv-callback-callback"    => "bvCallbackAvailableEmailCanInvite",
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 200,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                        'required'                     => false,
                    ]) ?>
                <?php endfor; ?>

                <div class="hidden">
                    <div class="form-group" id="EmailFormGroup">
                        <label for="" class="col col-sm-3 control-label form-label"></label>
                        <?=
                        $this->Form->input('Team.emails.1000', [
                            'label'                        => false,
                            'div'                          => false,
                            'type'                         => 'string',
                            'placeholder'                  => 'name@domain.com',
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
                </div>

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
            <?php $this->Form->unlockField("Team.emails") ?>
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
                })
                    .on('click', '#AddEmail', function (e) {
                        e.preventDefault();
                        var $obj = $(this);
                        var index = parseInt($obj.attr("index"));
                        //clone
                        var $email_form_group = $('#EmailFormGroup').clone();
                        $email_form_group.find('input').attr('name', 'data[Team][emails][' + index + ']');
                        $email_form_group.appendTo('.panel-body');
                        $('#InviteTeamForm').bootstrapValidator('addField', 'data[Team][emails][' + index + ']');
                        if ($obj.attr('max_index') != undefined && index >= parseInt($obj.attr('max_index'))) {
                            $obj.remove();
                        }
                        //increment
                        $obj.attr('index', index + 1);

                    });
            });
        </script>
        <?php $this->end() ?>
    </div>
</div>
<!-- END app/View/Teams/invite.ctp -->
