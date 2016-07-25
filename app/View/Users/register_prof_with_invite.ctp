<?php /**
 * ユーザ登録画面
 *
 * @var CodeCompletionView $this
 * @var                    $last_first
 * @var                    $email
 * @var                    $team_name
 */
?>
<!-- START app/View/Users/register_prof_with_invite.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __('Join the Goalous team for "%s"?', $team_name) ?></div>
            <?=
            $this->Form->create('User', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label form-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control register_input-design'
                ],
                'class'         => 'form-horizontal validate',
                'novalidate'    => true
            ]); ?>
            <div class="panel-body register-panel-body">
                <div class="form-group">
                    <?= __('Your name will displayed along with your goals and posts in Goalous') ?>
                </div>
                <?php //姓と名は言語によって表示順を変える
                $last_name = $this->Form->input('last_name', [
                    'label'                        => __("Last Name"),
                    'placeholder'                  => __("eg. Armstrong"),
                    "pattern"                      => '^[a-zA-Z]+$',
                    "data-bv-regexp-message"       => __("Only alphabet characters are allowed."),
                    "data-bv-notempty"             => "true",
                    "data-bv-notempty-message"     => __("Input is required."),
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 128,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
                    'required'                     => false,
                ]);
                $first_name = $this->Form->input('first_name', [
                    'label'                        => __("First Name"),
                    'placeholder'                  => __("eg. Harry"),
                    "pattern"                      => '^[a-zA-Z]+$',
                    "data-bv-regexp-message"       => __("Only alphabet characters are allowed."),
                    "data-bv-notempty"             => "true",
                    "data-bv-notempty-message"     => __("Input is required."),
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 128,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
                    'required'                     => false,
                ]);
                if ($last_first) {
                    echo $last_name;
                    echo $first_name;
                } else {
                    echo $first_name;
                    echo $last_name;
                }
                ?>
                <hr>
                <?php $tosLink = $this->Html->link(__('Terms of Use'),
                    [
                        'controller' => 'pages',
                        'action'     => 'display',
                        'pagename'   => 'terms',
                    ],
                    [
                        'target'  => "_blank",
                        'onclick' => "window.open(this.href,'_system');return false;",
                        'class'   => 'link',
                    ]
                );

                $ppLink = $this->Html->link(__('Privacy Policy'),
                    [
                        'controller' => 'pages',
                        'action'     => 'display',
                        'pagename'   => 'privacy_policy',
                    ],
                    [
                        'target'  => "_blank",
                        'onclick' => "window.open(this.href,'_system');return false;",
                        'class'   => 'link',
                    ]
                );
                echo $this->Form->input('agree_tos', [
                    'wrapInput' => 'col col-sm-9 col-sm-offset-3',
                    'type'      => 'checkbox',
                    'label'     => [
                        'class' => null,
                        'text'  => __("I agree to %s and %s of Goalous.", $tosLink, $ppLink)
                    ],
                    'class'     => 'validate-checkbox',
                    'required'  => false,
                ]);
                //タイムゾーン設定の為のローカル時刻をセット
                echo $this->Form->input('local_date', [
                    'label' => false,
                    'div'   => false,
                    'style' => 'display:none;',
                    'id'    => 'InitLocalDate',
                ]);
                ?>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->button(__('Next') . ' <i class="fa fa-angle-right"></i>',
                            ['type' => 'submit', 'class' => 'btn btn-primary', 'disabled' => 'disabled']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<?php $this->append('script'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        //ユーザ登録時にローカル時間をセットする
        $('input#InitLocalDate').val(getLocalDate());

        // 登録可能な email の validate
        require(['validate'], function (validate) {
            window.bvCallbackAvailableEmail = validate.bvCallbackAvailableEmail;
        });
    });


</script>
<?php $this->end(); ?>
<!-- END app/View/Users/register_prof_with_invite.ctp -->
