<!-- START app/View/Signup/password.ctp -->
        <div class="row">
            <div class="panel panel-default panel-signup">
                <div class="panel-heading signup-title">Set your password</div>
                <div class="signup-description">password sample text.password sample text.password sample text.</div>

                <div class="panel-heading signup-itemtitle">Password</div>
                <form action="#" class="form-horizontal validate" novalidate="novalidate" id="" method="post" accept-charset="utf-8">

                    <input name="data[User][password]" class="form-control signup_input-design" placeholder="********" maxlength="50" type="password" id="UserPassword" required="required" data-bv-field="data[User][password]">
                    <div class="signup-description mod-small">8文字以上。 アルファベット大文字、小文字、数字が混在している必要があります。記号は使えません。</div>

                    <div class="signup-error-description">
                        <i class="fa fa-exclamation-circle signup-load-icon mod-error"></i>
                        パスワードの形式が正しくありません。<br/>数字の混在が必要です。
                    </div>
                    <div class="submit signup-btn"><input class="btn btn-primary signup-btn-submit" type="submit" value="次へ→" disabled="disabled"></div>
                    <div class="submit signup-btn"><input class="btn btn-primary signup-btn-submit" type="submit" value="次へ→"></div>

                </form>

            </div>
        </div>
<a href="/signup/team">ページ遷移用リンク</a>
<!-- END app/View/Signup/password.ctp -->
