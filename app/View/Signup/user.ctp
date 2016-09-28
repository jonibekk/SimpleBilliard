<?= $this->App->viewStartComment()?>
        <div class="row">
            <div class="panel panel-default panel-signup">
                <div class="panel-heading signup-title">What's your name?</div>
                <div class="signup-description">username sample text.username sample text.username sample text.username sample text.username sample text.username sample text.</div>


                <form action="#" class="form-horizontal validate" novalidate="novalidate" id="" method="post" accept-charset="utf-8">
                    <div class="panel-heading signup-itemtitle">your name</div>
                    <input name="data[LocalName][0][last_name]" class="form-control signup_input-design" placeholder="例) 鈴木" data-bv-stringlength="true" data-bv-stringlength-max="128" data-bv-stringlength-message="最大文字数(128)を超えています。" maxlength="128" type="text" value="" id="LocalName0LastName">
                    <input name="data[LocalName][0][last_name]" class="form-control signup_input-design" placeholder="例) 花子" data-bv-stringlength="true" data-bv-stringlength-max="128" data-bv-stringlength-message="最大文字数(128)を超えています。" maxlength="128" type="text" value="" id="LocalName0LastName">
                    <input name="data[LocalName][0][last_name]" class="form-control signup_input-design" placeholder="例) すずき" data-bv-stringlength="true" data-bv-stringlength-max="128" data-bv-stringlength-message="最大文字数(128)を超えています。" maxlength="128" type="text" value="" id="LocalName0LastName">
                    <input name="data[LocalName][0][last_name]" class="form-control signup_input-design" placeholder="例) はなこ" data-bv-stringlength="true" data-bv-stringlength-max="128" data-bv-stringlength-message="最大文字数(128)を超えています。" maxlength="128" type="text" value="" id="LocalName0LastName">

                    <div class="checkbox signup-checkbox"><input type="hidden" name="data[User][agree_tos]" id="UserAgreeTos_" value="0"><label for="UserAgreeTos"><input type="checkbox" name="data[User][agree_tos]" class="validate-checkbox" value="1" id="UserAgreeTos" data-bv-field="validate-checkbox"> Goalousの<a href="/terms" target="_blank" onclick="window.open(this.href,'_system');return false;" class="link">利用規約</a>と<a href="/privacy_policy" target="_blank" onclick="window.open(this.href,'_system');return false;" class="link">プライバシーポリシー</a>に同意します。</label></div>
                    <div class="signup-error-description">
                        <i class="fa fa-exclamation-circle signup-load-icon mod-error"></i>
                        利用規約に同意してください。
                    </div>
                    <div class="submit signup-btn"><input class="btn btn-lightGray signup-btn-submit" type="submit" value="次へ→" disabled="disabled"></div>
                    <div class="submit signup-btn"><input class="btn btn-primary signup-btn-submit" type="submit" value="次へ→"></div>

                </form>

            </div>
        </div>
<a href="/signup/password">ページ遷移用リンク</a>
<?= $this->App->viewEndComment()?>
