<?= $this->App->viewStartComment() ?>
<div class="row">
  <div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
      <div class="panel-body login-panel-body demo-login">
        <h1 class="demo-login-title">
            <?= __("Welcome to the Demo Site!") ?><br>
        </h1>
        <p class="demo-login-summary">
            <?= __("Try out Goalous's key features such as user invitations, <br>goal creation and actions, and circle postings.") ?>
        </p>
        <div id="RequireCookieAlert" class="alert alert-danger" style="display:none">
            <?= __("Please enable cookie.") ?>
        </div>
          <?=
          $this->Form->create('User', [
              'inputDefaults' => [
                  'div'       => 'form-group',
                  'label'     => [
                      'class' => 'col col-sm-3 control-label form-label'
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
              $this->Form->submit(__("Login"),
                  ['class' => 'btn btn-primary' /*, 'disabled'=>'disabled'*/]) ?>
          </div>
        </div>
        <?= $this->Form->end(); ?>
        <div class="demo-login-warning">
          <span class="demo-login-warning-title"><i class="warning fa-warning fa fa-5 mr_4px"></i><?= __("Notes on use")?></span>
          <p>
            <?= __("The demo environment is a shared environment with other customers.")?><br>
            <?= __("Please do not post confidential or personal information.") ?></p><br>
            <?= __("Posted data is reset every %s hours. It will not be stored for a long time.", DEMO_RESET_HOURS) ?><br>
            <?= __("Please review the <a href='/terms' target='_blank'> Terms of Service </a> before using.")?>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->App->viewEndComment() ?>
