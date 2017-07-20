<?php
//TODO:delete this file
//Test method for showing markup html
?>
<?= $this->App->viewStartComment() ?>

<div id="UserInvite">
    <section class="panel panel-default mod-form col-sm-8 col-sm-offset-2 clearfix gl-form">
        <form class="">
            <div class="mb_32px">
                <p class="title">招待先メールアドレス(3)</p>
                <ul class="ml_5px">
                    <li>test1@example.com</li>
                    <li>test1@example.com</li>
                    <li>test1@example.com</li>
                    <li>test1@example.com</li>
                </ul>
            </div>
            <div class="mb_32px">
                <p class="title">請求料金</p>
                <div class="ml_5px">
                    <dl class="totalCharge">
                        <dt class="totalCharge-label">請求料金合計</dt>
                        <dd class="totalCharge-value">¥3,860</dd>
                    </dl>
                    <div class="totalChargeFormula mb_12px">
                        <div class="totalChargeFormula-block">
                            <span class="totalChargeFormula-num">3<span class="totalChargeFormula-unit">people</span></span>
                        </div>
                        <div class="totalChargeFormula-block">
                            <span class="totalChargeFormula-symbol">×</span>
                        </div>
                        <div class="totalChargeFormula-block">
                            <span class="totalChargeFormula-num">¥1,980</span>
                        </div>
                        <div class="totalChargeFormula-block">
                            <span class="totalChargeFormula-symbol">×</span>
                        </div>
                        <div class="totalChargeFormula-block">
                            <div class="totalChargeFormula-fraction">
                                <span class="totalChargeFormula-num">20<span class="totalChargeFormula-unit">days</span></span>
                                <span class="totalChargeFormula-fraction-exp"></span>
                                <span class="totalChargeFormula-num">1<span class="totalChargeFormula-unit">month</span></span>
                            </div>
                        </div>
                    </div>
                    <table class="totalChargeFormulaDetail mb_16px">
                        <tr>
                            <th class="totalChargeFormulaDetail-item">3 people</th>
                            <td class="totalChargeFormulaDetail-description">：請求人数</td>
                        </tr>
                        <tr>
                            <th class="totalChargeFormulaDetail-item">¥1,980</th>
                            <td class="totalChargeFormulaDetail-description">：1ヶ月の利用料金</td>
                        </tr>
                        <tr>
                            <th class="totalChargeFormulaDetail-item">20 days</th>
                            <td class="totalChargeFormulaDetail-description">：利用日数(日割り)</td>
                        </tr>
                    </table>
                    <a href="">
                        <i class="fa fa-question-circle mr_4px" aria-hidden="true"></i>詳細はこちら
                    </a>
                </div>
<!--                <dl class="totalChargeFormulaDetail">-->
<!--                    <dt class="totalChargeFormulaDetail-item">3people</dt>-->
<!--                    <dd class="totalChargeFormulaDetail-description">請求人数</dd>-->
<!--                    <dt class="totalChargeFormulaDetail-item">¥1,980</dt>-->
<!--                    <dd class="totalChargeFormulaDetail-description">1ヶ月の利用料金</dd>-->
<!--                    <dt class="totalChargeFormulaDetail-item">20 days</dt>-->
<!--                    <dd class="totalChargeFormulaDetail-description">利用日数(日割り)</dd>-->
<!--                </dl>-->
            </div>
            <div class="serviceTermAgreement mb_8px">
                <label><input type="checkbox" name="service_term_agreement" class="serviceTermAgreement-cb"><a href="#">利用規約</a>に同意する</label>
            </div>
            <div class="btnGroupForForm">
                <button type="submit" class="btnGroupForForm-next">送信する</button>
                <a class="btnGroupForForm-cancel" href="/">戻る</a>
            </div>
        </form>
    </section>
</div>

<?= $this->App->viewEndComment() ?>
