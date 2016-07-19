import React from 'react'
import { Link } from 'react-router'

export default class Auth extends React.Component {
  render() {
    return (
      <div>
      <div className="row">
        { /* <!-- START app/webroot/js/react/signup/components/auth.js --> */}
          <div className="panel panel-default panel-signup">
              <div className="panel-heading signup-title">Check your email!</div>
              <div className="signup-description">Auth code sample text.Auth code sample textAuth code sample textAuth code sample textAuth code sample textAuth code sample text.</div>

              <form action="#" className="form-horizontal validate" novalidate="novalidate" id="" method="post" accept-charset="utf-8">
                  <div className="signup-auth-split_input signup-auth-large_margin" data-multi-input-code="true">
                      <div className="confirmation_code_group">
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$" />
                          </div>
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$" />
                          </div>
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$" />
                          </div>
                      </div>

                      <div className="signup-auth-confirmation_code_span_cell">—</div>

                      <div className="confirmation_code_group">
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$" />
                          </div>
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$" />
                          </div>
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" maxlength="1" pattern="^[0-9]+$" />
                          </div>
                      </div>
                  </div>
              </form>

              <div className="signup-load-description">
                  <div className="signup-load-icon">
                      <img src="/img/ajax-loader.gif" />
                  </div>
                  loading...原稿の長さによっては中央合わせ
              </div>
              <div className="signup-load-description mod-error">
                  <i className="fa fa-exclamation-circle signup-load-icon mod-error"></i> Auth code sample text.Auth code sample text Auth code sample text.こちらも原稿の長さによっては中央合わせに変更します。現在は左寄せ。
              </div>
              <div className="signup-load-description mod-lock">
                  <i className="fa fa-lock signup-load-icon mod-error"></i> Auth code sample text.こちらも原稿の長さによっては中央合わせに変更します。現在は左寄せ。
              </div>

              <div className="signup-description">Auth code sample text.Auth code sample textAuth code sample textAuth code sample textAuth code sample textAuth code sample text.</div>
          </div>
      </div>
      <Link to="/signup/user">ページ遷移用リンク</Link>
      {/* <!-- END app/webroot/js/react/signup/components/auth.js --> */ }
      </div>
    )
  }
}
