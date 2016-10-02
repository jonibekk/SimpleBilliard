<?= $this->App->viewStartComment()?>
        <div class="row">
            <div class="panel panel-default panel-signup">
                <div class="panel-heading signup-title">What do you want to call your Goalous team?</div>
                <div class="signup-description">Goalous team name sample text.Goalous team name sample text.Goalous team name sample text.Goalous team name sample text.Goalous team name sample text.<br/>
                Goalous team name sample text.Goalous team name sample text.Goalous team name sample text.</div>

                <form action="#" class="form-horizontal validate" novalidate="novalidate" id="" method="post" accept-charset="utf-8">
                    <div class="panel-heading signup-itemtitle">Goalous team name</div>
                    <input data-bv-field="data[Team][name]" name="data[Team][name]" class="form-control signup_input-design" placeholder="例) チームGoalous" data-bv-notempty-message="入力必須項目です。" data-bv-stringlength="true" data-bv-stringlength-max="128" maxlength="128" id="TeamName" required="required" type="text">

                    <div class="signup-error-description">
                        <i class="fa fa-exclamation-circle signup-load-icon mod-error"></i>
                        最大文字数(128)を超えています。
                    </div>
                    <div class="submit signup-btn"><input class="btn btn-lightGray signup-btn-submit" type="submit" value="次へ→" disabled="disabled"></div>
                    <div class="submit signup-btn"><input class="btn btn-primary signup-btn-submit" type="submit" value="次へ→"></div>

                </form>

            </div>
        </div>
<a href="/signup/term">ページ遷移用リンク</a>
<?= $this->App->viewEndComment()?>
