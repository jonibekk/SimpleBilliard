<?= $this->App->viewStartComment() ?>
<div class="row">
  <div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default demo-login">
      <div class="panel-heading">
        <h1 class="demo-login-title">
            <?= __("Welcome to the Demo Site!") ?><br>
        </h1>
        <p class="demo-login-summary">
            <?= __("Try out Goalous's key features such as user invitations, goal creation and actions, and circle postings.") ?>
        </p>
      </div>
      <div class="panel-body login-panel-body">
        <div id="RequireCookieAlert" class="alert alert-danger" style="display:none">
            <?= __("Please enable cookie.") ?>
        </div>
        <div class="demo-login-warning">
          <span class="demo-login-warning-title"><i class="warning fa-warning fa fa-5 mr_4px"></i><?= __("Notes on use")?></span>
          <p>
              <?= __("Please do not post confidential or personal information because the demo environment is a shared environment with other customers.") ?>
            <?= __("Also, posted data will be reset in %s hours.", DEMO_RESET_HOURS) ?><br><br>
            <?= __("By clicking <q>I Agree. Login.</q> below, you are agreeing to the <a href='/terms' target='_blank'>Terms&nbsp;of&nbsp;Service</a> and the <a href='/privacy_policy' target='_blank'>Privacy&nbsp;Policy</a>.")?>
        </div>

          <?=
          $this->Form->create('User', [
              'inputDefaults' => [
                  'div'       => 'form-group',
                  'label'     => [
                      'class' => 'col col-sm-3 control-label foIrm-label'
                  ],
                  'wrapInput' => 'col col-sm-6',
                  'class'     => 'form-control login_input-design disable-change-warning'
              ],
              'class'         => 'form-horizontal login-form demo-login-form',
              'novalidate'    => true
          ]); ?>
        <div class="form-group">
          <div class="">
              <?=
              $this->Form->submit(__("I Agree. Login."),
                  ['class' => 'btn btn-primary prl' /*, 'disabled'=>'disabled'*/]) ?>
          </div>
        </div>
        <?= $this->Form->end(); ?>
      </div>
    </div>
  </div>
</div>
<?= $this->App->viewEndComment() ?>
