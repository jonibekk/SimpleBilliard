<?= $this->App->viewStartComment() ?>
<section id="UserInvite" class="panel panel-default mod-form col-sm-8 col-sm-offset-2 clearfix gl-form">
        <form action="#" >
          <div class="inviteCfmBlock">
            <h2 class="title"><?= __('招待先メールアドレス');?> 5</h2>
            <ul class="ml_5px">
              <li>goalous.test01@gmail.com</li>
              <li>goalous.test02@gmail.com</li>
              <li>goalous.test03@gmail.com</li>
              <li>goalous.test04@gmail.com</li>
              <li>goalous.test05@gmail.com</li>
            </ul>
          </div>
          <!-- {is_paid_plan && -->
          <div class="inviteCfmBlock mod-bdt">
            <h2 class="title"><?= __('請求料金'); ?></h2>
            <div class="ml_5px">
              <dl class="totalCharge">
                <dt class="totalCharge-label"><?('請求料金合計');?></dt>
                <dd class="totalCharge-value">&yen; 190,800</dd>
              </dl>
              <div class="totalChargeFormula mb_12px">
                <div class="totalChargeFormula-block">
                  <span class="totalChargeFormula-num">
                    15
                    <span class="totalChargeFormula-unit">people</span>
                  </span>
                </div>
                <div class="totalChargeFormula-block">
                  <span class="totalChargeFormula-symbol">×</span>
                </div>
                <div class="totalChargeFormula-block">
                  <span class="totalChargeFormula-num">&yen;1,980</span>
                </div>
                <div class="totalChargeFormula-block">
                  <span class="totalChargeFormula-symbol">×</span>
                </div>
                <div class="totalChargeFormula-block">
                  <div class="totalChargeFormula-fraction">
                    <span class="totalChargeFormula-num">30<span
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
                  <th class="totalChargeFormulaDetail-item">15 people</th>
                  <td class="totalChargeFormulaDetail-description">：請求人数</td>
                </tr>
                <tr>
                  <th class="totalChargeFormulaDetail-item">&yen;1,980</th>
                  <td class="totalChargeFormulaDetail-description">：<?+ __('1ヶ月の利用料金'); ?></td>
                </tr>
                <tr>
                  <th class="totalChargeFormulaDetail-item">30 days</th>
                  <td class="totalChargeFormulaDetail-description">：<?= __('利用日数(日割り)'); ?></td>
                </tr>
                </tbody>
              </table>
              <a href="#">
                <i class="fa fa-question-circle mr_4px" aria-hidden="true"></i><?= __('詳細はこちら');?>
              </a>
            </div>
          </div>
          <div class="serviceTermAgreement mb_8px">
            <label>
              <input type="checkbox" name="service_term_agreement" class="serviceTermAgreement-cb"/><?= __('請求内容を確認しました'); ?>
            </label>
          </div>
          <div class="btnGroupForForm">
            <button type="submit" class="btnGroupForForm-next" ref="submit">
              <?= __('送信する'); ?>
            </button>
            <a class="btnGroupForForm-cancel" to="/users/invite"><?= __('戻る'); ?></a>
          </div>
        </form>
      </section>

<?= $this->App->viewEndComment() ?>