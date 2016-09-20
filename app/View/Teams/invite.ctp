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
<?= $this->App->viewStartComment()?>
<div class="row">
    <div class="panel panel-default panel-signup">
        <div class="panel-heading signup-title"><?= __('Send Invitations') ?></div>
        <?=
        $this->Form->create('Team', [
            'inputDefaults' => [
                'wrapInput' => 'col col-sm-6 signup-email-input-wrap',
                'class'     => 'form-control signup_input-design'
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
            'id'            => 'InviteTeamForm',
            'url'           => ['action' => 'invite'],
            'method'        => 'post'
        ]); ?>

        <div class="signup-description"><?= __('Invite coworkers to your team. Goalous works best when you work with others.') ?></div>

        <?php $default_email_count = 3 ?>
        <div class="signup-invite-form-header">
            <label for="TeamEmails"
                   class="col col-xxs-8 col-sm-3 control-label form-label"><?= __("Email address") ?></label>
            <div class="col col-xxs-4 col-sm-6">
                <a href="#" class="form-control-static pull-right" id="AddEmail"
                   index="<?= $default_email_count ?>" max_index="100"><i
                        class="fa fa-plus"></i> <?= __('Add email') ?></a>
            </div>
        </div>
        <div class="signup-invite-email-list">
            <?php for ($i = 0; $i < $default_email_count; $i++): ?>
                <?=
                $this->Form->input("Team.emails.$i", [
                    'label'                        => false,
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
        </div>

        <div class="hidden">
            <div class="form-group" id="EmailFormGroup">
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




        <div className="submit">
          <?= $this->Form->button(__('Next') . ' <i class="fa fa-angle-right"></i>',
              [
                  'type'     => 'submit',
                  'class'    => 'btn btn-primary signup-invite-submit-button',
                  'disabled' => 'disabled',
                  'escape'   => false
              ]) ?>
          <?php if (isset($from_setting) && !$from_setting): ?>
              <?=
              $this->Html->link(__("Skip for Now"),
              ['controller' => 'teams', 'action' => 'invite_skip'],
              ['class' => 'btn signup-btn-skip', 'div' => false])
              ?>
          <?php endif; ?>
        </div>
      <?= $this->Form->end(); ?>
    </div>
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
            $email_form_group.appendTo('.signup-invite-email-list');
            $('#InviteTeamForm').bootstrapValidator('addField', 'data[Team][emails][' + index + ']');
            if ($obj.attr('max_index') != undefined && index >= parseInt($obj.attr('max_index'))) {
                $obj.remove();
            }
            //increment
            $obj.attr('index', index + 1);

        });

    });
</script>
<?php $this->Form->unlockField("Team.emails") ?>
<?php $this->end() ?>
<?= $this->App->viewEndComment()?>
