<?= $this->App->viewStartComment() ?>
<div class="row">
  <div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
      <div class="panel-body login-panel-body demo-login">
        <h1 class="demo-login-title">
            <?= __("Welcome to the Demo Site!") ?><br>
        </h1>
        <p class="demo-login-summary">
            <?= __("Let's experience Goalous as Demo") ?>
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
        <p class="demo-login-warning">
          <span class="demo-login-warning-strong">
            <i class="fa-warning fa fa-5 mr_4px"></i><?= __("This is free account to experience demo.")?><br>
            <?= __("Please be careful not to enter personal information or confidential information.") ?></span><br>
            <?= __("Demo site data is removed every %s hours.", DEMO_RESET_HOURS) ?>
        </p>
      </div>
    </div>
  </div>
</div>
<?= $this->App->viewEndComment() ?>
