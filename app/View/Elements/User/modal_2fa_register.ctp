<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $url_2fa
 */
?>

<style type="text/css">

    /*.two_fa_step_box {
        border-bottom-style: dashed;
        border-bottom-width: thin;
        margin-bottom: 18px;
        margin-top: 5px;
        padding: 18px;
    }*/
    .two_fa_title {

    }

    .two_fa_app_download_link {
        color: #6495ED;
    }
</style>

<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("2-Step Verification settings") ?></h4>
        </div>
        <?=
        $this->Form->create('User', [
            'url'           => ['controller' => 'users', 'action' => 'register_2fa'],
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'col col-sm-3 modal-label pr_12px'
                ],
                'wrapInput' => 'col col-sm-6',
                'class'     => 'form-control'
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
        ]); ?>
        <div class="modal-body" style="max-height: none">
            <div class="two_fa_step_box">
                <label for=""><?= __("STEP 1 : ") ?><?= __("Install Application for 2-Step Verification") ?></label>
                <p><?= __('Download Google Authenticator') ?></p>
                <div class="modal_two_fa_download">
                    <div class="btn-frame">
                        <p><i class="fa fa-android"></i> Android</p>
                        <a class="two_fa_app_download_link"
                           href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"
                           target="_blank"><?= __("Download") ?></a>
                    </div>
                    <div class="btn-frame">
                        <p><i class="fa fa-apple"></i> iOS</p>
                        <a class="two_fa_app_download_link"
                           href="http://itunes.apple.com/us/app/google-authenticator/id388497605"
                           target="_blank"><?= __("Download") ?></a>
                    </div>
                </div>
            </div>

            <div class="two_fa_step_box">
                <label for=""><?= __("STEP 2 : ") ?><?= __("Scan QR") ?></label>
                <p><?= __("Start the app and read QR by camera.") ?></p>
                <img src="<?=$url_2fa?>" width="120" height="120">
            </div>

            <div class="two_fa_step_box">
                <label for=""><?= __("STEP 3 : ") ?><?= __("Enter confirmation code") ?></label>
                <p><?= __("After reading QR, enter the code (6 digits)") ?></p>
                <?=
                $this->Form->input('2fa_code',
                    [
                        'label'                    => false,
                        'placeholder'              => __("code"),
                        "data-bv-notempty-message" => __("Input is required."),
                        'required'                 => true,
                    ]) ?>
            </div>
        </div>
        <div class="modal-footer">
            <?=
            $this->Form->submit(__("Next"),
                ['class' => 'btn btn-primary pull-right', 'div' => false,]) ?>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
