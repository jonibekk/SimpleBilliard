<?= $this->App->viewStartComment()?>
        <div class="row">
            <div class="panel panel-default panel-signup">
                <div class="panel-heading signup-title">Check your email!</div>
                <div class="signup-description">Auth code sample text.Auth code sample textAuth code sample textAuth code sample textAuth code sample textAuth code sample text.</div>

                <form action="#" class="form-horizontal validate" novalidate="novalidate" id="" method="post" accept-charset="utf-8">
                        <div class="signup-auth-split_input signup-auth-large_margin" data-multi-input-code="true">
            				<div class="confirmation_code_group">
            					<div class="signup-auth-split_input_item signup-auth-input_wrapper"><input type="text" class="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$"></div>
            					<div class="signup-auth-split_input_item signup-auth-input_wrapper"><input type="text" class="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$"></div>
            					<div class="signup-auth-split_input_item signup-auth-input_wrapper"><input type="text" class="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$"></div>
            				</div>

				            <div class="signup-auth-confirmation_code_span_cell">—</div>

            				<div class="confirmation_code_group">
            					<div class="signup-auth-split_input_item signup-auth-input_wrapper"><input type="text" class="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$"></div>
            					<div class="signup-auth-split_input_item signup-auth-input_wrapper"><input type="text" class="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$"></div>
            					<div class="signup-auth-split_input_item signup-auth-input_wrapper"><input type="text" class="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$"></div>
            				</div>
			            </div>
                </form>

                <div class="signup-load-description">
                    <div class="signup-load-icon"><img src="/img/ajax-loader.gif"></div>
                    loading...原稿の長さによっては中央合わせ
                </div>
                <div class="signup-load-description mod-error">
                    <i class="fa fa-exclamation-circle signup-load-icon mod-error"></i>
                    Auth code sample text.Auth code sample text Auth code sample text.こちらも原稿の長さによっては中央合わせに変更します。現在は左寄せ。
                </div>
                <div class="signup-load-description mod-lock">
                    <i class="fa fa-lock signup-load-icon mod-error"></i>
                    Auth code sample text.こちらも原稿の長さによっては中央合わせに変更します。現在は左寄せ。
                </div>

                <div class="signup-description">Auth code sample text.Auth code sample textAuth code sample textAuth code sample textAuth code sample textAuth code sample text.</div>
            </div>
        </div>
<a href="/signup/user">ページ遷移用リンク</a>
<?= $this->App->viewEndComment()?>
