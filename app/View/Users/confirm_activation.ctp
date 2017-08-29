<?= $this->App->viewStartComment() ?>
<section id="UserInvite" class="panel panel-default mod-form col-sm-8 col-sm-offset-2 clearfix gl-form">
        <form action="#" >
          <div class="inviteCfmBlock">
            <h2 class="title"><?= __('招待先メールアドレス');?>({emails.length})</h2>
            <ul class="ml_5px">
              {emails_el}
            </ul>
          </div>
          <!-- {is_paid_plan && -->
          <div class="inviteCfmBlock mod-bdt">
            <h2 class="title"><?= __('請求料金'); ?></h2>
            <div class="ml_5px">
              <dl class="totalCharge">
                <dt class="totalCharge-label"><?('請求料金合計');?></dt>
                <dd class="totalCharge-value">{confirm_data.total_charge}</dd>
              </dl>
              <div class="totalChargeFormula mb_12px">
                <div class="totalChargeFormula-block">
                  <span class="totalChargeFormula-num">
                    {confirm_data.charge_users_count}
                    <span class="totalChargeFormula-unit">people</span>
                  </span>
                </div>
                <div class="totalChargeFormula-block">
                  <span class="totalChargeFormula-symbol">×</span>
                </div>
                <div class="totalChargeFormula-block">
                  <span class="totalChargeFormula-num">{confirm_data.amount_per_user}</span>
                </div>
                <div class="totalChargeFormula-block">
                  <span class="totalChargeFormula-symbol">×</span>
                </div>
                <div class="totalChargeFormula-block">
                  <div class="totalChargeFormula-fraction">
                    <span class="totalChargeFormula-num">{confirm_data.use_days_by_next_base_date}<span
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
                  <th class="totalChargeFormulaDetail-item">{confirm_data.charge_users_count} people</th>
                  <td class="totalChargeFormulaDetail-description">：請求人数</td>
                </tr>
                <tr>
                  <th class="totalChargeFormulaDetail-item">{confirm_data.amount_per_user}</th>
                  <td class="totalChargeFormulaDetail-description">：1ヶ月の利用料金</td>
                </tr>
                <tr>
                  <th class="totalChargeFormulaDetail-item">{confirm_data.use_days_by_next_base_date} days</th>
                  <td class="totalChargeFormulaDetail-description">：利用日数(日割り)</td>
                </tr>
                </tbody>
              </table>
              <a href>
                <i class="fa fa-question-circle mr_4px" aria-hidden="true"/>詳細はこちら
              </a>
            </div>
          </div>
          <!-- }{is_paid_plan && -->
          <div class="serviceTermAgreement mb_8px">
            <label>
              <input
                type="checkbox" name="service_term_agreement" class="serviceTermAgreement-cb"
                onChange={(e) => this.onCheckAgreement(e)}
              />請求内容を確認しました
            </label>
          </div>
          <!-- } -->
          <div class="btnGroupForForm">
            <button type="submit" class="btnGroupForForm-next" ref="submit"
                    disabled={(is_paid_plan && !this.state.check_agreement) || is_saving ? "disabled" : ""}>
              送信する
            </button>
            <a class="btnGroupForForm-cancel" to="/users/invite">戻る</a>
          </div>
        </form>
      </section>

<?= $this->App->viewEndComment() ?>