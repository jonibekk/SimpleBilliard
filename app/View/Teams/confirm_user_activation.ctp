<?= $this->App->viewStartComment() ?>
<?php
// TODO.Payment: 
// 1. Fixing design collaposed
// 2. Translation
?>
<section id="UserInvite" class="panel panel-default mod-form col-sm-8 col-sm-offset-2 clearfix gl-form">
  <div class="inviteCfmBlock">
    <h2 class="title"><?= __('Activation user');?></h2>
    <div class="ml_5px">
      <p><?= $displayUserName ?></p>
    </ul>
  </div>
  <!-- {is_paid_plan && -->
  <div class="inviteCfmBlock mod-bdt">
    <h2 class="title"><?= __('請求料金'); ?></h2>
    <div class="ml_5px">
      <dl class="totalCharge">
        <dt class="totalCharge-label"><?('請求料金合計');?></dt>
        <dd class="totalCharge-value"><?= $totalCharge ?></dd>
      </dl>
      <div class="totalChargeFormula mb_12px">
        <div class="totalChargeFormula-block">
          <span class="totalChargeFormula-num"><?= $amountPerUser ?></span>
        </div>
        <div class="totalChargeFormula-block">
          <span class="totalChargeFormula-symbol">×</span>
        </div>
        <div class="totalChargeFormula-block">
          <div class="totalChargeFormula-fraction">
            <span class="totalChargeFormula-num"><?= $useDaysByNext ?><span
              class="totalChargeFormula-unit">days</span></span>
            <span class="totalChargeFormula-fraction-exp"/>
            <span class="totalChargeFormula-num">1<span
              class="totalChargeFormula-unit">month</span></span>
          </div>
        </div>
      </div>
      <table class="totalChargeFormulaDetail mb_16px">
        <tbody>
        <tr>
          <th class="totalChargeFormulaDetail-item"><?= $amountPerUser ?></th>
          <td class="totalChargeFormulaDetail-description">：<?= __('1ヶ月の利用料金'); ?></td>
        </tr>
        <tr>
          <th class="totalChargeFormulaDetail-item"><?= $useDaysByNext ?> days</th>
          <td class="totalChargeFormulaDetail-description">：<?= __('利用日数(日割り)'); ?></td>
        </tr>
        </tbody>
      </table>
      <a href="#" target="_blank">
        <i class="fa fa-question-circle mr_4px" aria-hidden="true"></i><?= __('詳細はこちら');?>
      </a>
    </div>
  </div>
  <div class="serviceTermAgreement mb_8px">
    <label>
      <input type="checkbox" name="service_term_agreement" class="serviceTermAgreement-cb js-required-agreement"/><?= __('請求内容を確認しました'); ?>
    </label>
  </div>
  <div class="btnGroupForForm">
  <?= $this->Form->create('TeamMember', [
        'novalidate' => true,
        'url'        => ['controller' => 'teams', 'action' => 'activate_with_payment'],
    ]); ?>
    <?= $this->Form->hidden('id', ['value' => $teamMemberId]) ?>
    <?= $this->Form->submit(__("Send"),
                    ['class' => 'btnGroupForForm-next js-activate-submit', 'div' => false, 'disabled' => 'disabled']) ?>
    <?= $this->Form->end(); ?>
    <a class="btnGroupForForm-cancel" href="/teams/main"><?= __('戻る'); ?></a>
  </div>
</section>

<?= $this->App->viewEndComment() ?>