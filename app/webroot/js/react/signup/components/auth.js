import React from 'react'
import ReactDOM from 'react-dom'
import { Link } from 'react-router'

export default class Auth extends React.Component {
  constructor(props) {
    super(props)
    this.state = this.props.auth.code_list
  }
  inputCode(ref, index) {
    const key_name = `code${index}`
    const code = ReactDOM.findDOMNode(ref).value.trim()

    // 数字以外の場合は何もしない
    if(Number.isNaN(parseInt(code))) {
      return
    }

    this.setState({[key_name]: code}, () => {
      this.props.inputCode(index, code)
    })
    
    if(index < 6) {
      return ReactDOM.findDOMNode(this.refs[`code${index + 1}`]).focus()
    }
  }
  render() {
    const checking_box = () => {
      return (
        <div className="signup-load-description">
            <div className="signup-load-icon">
                <img src="/img/ajax-loader.gif" />
            </div>
            loading...原稿の長さによっては中央合わせ
        </div>
      )
    }
    const alert_box = () => {
      return (
        <div className="signup-load-description mod-error">
            <i className="fa fa-exclamation-circle signup-load-icon mod-error"></i> {this.props.auth.invalid_message}
        </div>
      )
    }
    const locked_box = () => {
      return (
        <div className="signup-load-description mod-lock">
            <i className="fa fa-lock signup-load-icon mod-error"></i> Auth code sample text.こちらも原稿の長さによっては中央合わせに変更します。現在は左寄せ。
        </div>
      )
    }

    return (
      <div>
      <div className="row">
        { /* <!-- START app/webroot/js/react/signup/components/auth.js --> */}
          <div className="panel panel-default panel-signup">
              <div className="panel-heading signup-title">Check your email!</div>
              <div className="signup-description">Auth code sample text.Auth code sample textAuth code sample textAuth code sample textAuth code sample textAuth code sample text.</div>

              <form action="#" className="form-horizontal validate" id="" method="post" acceptCharset="utf-8">
                  <div className="signup-auth-split_input signup-auth-large_margin" data-multi-input-code="true">
                      <div className="confirmation_code_group">
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" ref="code1" maxLength="1" type="text" value={this.state.code1} onChange={ () => this.inputCode(this.refs.code1, 1)} />
                          </div>
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" ref="code2" maxLength="1" type="text" value={this.state.code2} onChange={ () => this.inputCode(this.refs.code2, 2)} />
                          </div>
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" ref="code3" maxLength="1" type="text" value={this.state.code3} onChange={ () => this.inputCode(this.refs.code3, 3)} />
                          </div>
                      </div>

                      <div className="signup-auth-confirmation_code_span_cell">—</div>

                      <div className="confirmation_code_group">
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" ref="code4" maxLength="1" type="text" value={this.state.code4} onChange={ () => this.inputCode(this.refs.code4, 4)} />
                          </div>
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" ref="code5" maxLength="1" type="text" value={this.state.code5} onChange={ () => this.inputCode(this.refs.code5, 5)} />
                          </div>
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                              <input type="text" className="signup-auth-inline_input" ref="code6" maxLength="1" type="text" value={this.state.code6} onChange={ () => this.inputCode(this.refs.code6, 6)} />
                          </div>
                      </div>
                  </div>
              </form>

              {this.props.auth.checking_auth_code ? checking_box() : ''}
              {this.props.auth.auth_code_is_invalid ? alert_box() : ''}
              {this.props.auth.auth_code_is_locked || this.props.auth.auth_code_is_expired ? locked_box() : ''}

              <div className="signup-description">Auth code sample text.Auth code sample textAuth code sample textAuth code sample textAuth code sample textAuth code sample text.</div>
          </div>
      </div>
      <Link to="/signup/user">ページ遷移用リンク</Link>
      {/* <!-- END app/webroot/js/react/signup/components/auth.js --> */ }
      </div>
    )
  }
}
