import React from 'react'
import ReactDOM from 'react-dom'
import { Link } from 'react-router'

export default class Auth extends React.Component {
  constructor(props) {
    super(props)
    this.state = this.props.auth.code_list
  }

  inputCode(inputed_code, index) {

    // 数字以外の場合は何もしない
    if(Number.isNaN(parseInt(inputed_code))) {
      return
    }

    // 確認中の場合も何もしない
    if(this.props.auth.checking_auth_code) {
      return
    }

    // ペーストされた場合も含めてコードを確認する
    const code_splitted = inputed_code.split("")

    code_splitted.map((code, i) => {
      const key_name = `code${index + i}`
      const target_index = index + i

      if(code === '-') {
        return
      }

      if(target_index > 7) {
        return
      }

      this.setState({[key_name]: code}, () => {
        this.props.inputCode(target_index, code)
      })
    })

    // inputフィールドのフォーカス変更
    if(index < 6) {
      return ReactDOM.findDOMNode(this.refs[`code${index + 1}`]).focus()
    } else {
      return ReactDOM.findDOMNode(this.refs.code1).focus()
    }
  }

  componentWillReceiveProps(nextProps) {
    this.setState(nextProps.auth.code_list)
  }

  // componentDidUpdate() {
  //   if(this.props.auth.code_list['code1'] === '' && this.props.auth.code_list['code2'] === '' && this.props.auth.code_list['code3'] === '' && this.props.auth.code_list['code4'] === '' && this.props.auth.code_list['code5'] === '' && this.props.auth.code_list['code6'] === '') {
  //     return ReactDOM.findDOMNode(this.refs['code1']).focus()
  //   }
  // }

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
            <i className="fa fa-lock signup-load-icon mod-error"></i> {this.props.auth.locked_message}
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

              <div className="signup-auth-split_input signup-auth-large_margin" data-multi-input-code="true">
                  <div className="confirmation_code_group">
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code1" maxLength="1" type="text"
                                 value={this.state.code1}
                                 onKeyPress={ (event) => {this.inputCode(event.nativeEvent.key, 1)}}
                                 onPaste={ (event) => this.inputCode(event.clipboardData.getData('Text'), 1) } />
                      </div>
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code2" maxLength="1" type="text"
                                 value={this.state.code2}
                                 onKeyPress={ (event) => this.inputCode(event.nativeEvent.key, 2)}
                                 onPaste={ (event) => this.inputCode(event.clipboardData.getData('Text'), 2) } />
                      </div>
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code3" maxLength="1" type="text"
                          value={this.state.code3}
                          onKeyPress={ (event) => this.inputCode(event.nativeEvent.key, 3)}
                          onPaste={ (event) => this.inputCode(event.clipboardData.getData('Text'), 3) } />
                      </div>
                  </div>

                  <div className="signup-auth-confirmation_code_span_cell">—</div>

                  <div className="confirmation_code_group">
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code4" maxLength="1" type="text"
                          value={this.state.code4}
                          onKeyPress={ (event) => this.inputCode(event.nativeEvent.key, 4)}
                          onPaste={ (event) => this.inputCode(event.clipboardData.getData('Text'), 4) } />
                      </div>
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code5" maxLength="1" type="text"
                          value={this.state.code5}
                          onKeyPress={ (event) => this.inputCode(event.nativeEvent.key, 5)}
                          onPaste={ (event) => this.inputCode(event.clipboardData.getData('Text'), 5) } />
                      </div>
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code6" maxLength="1" type="text"
                          value={this.state.code6}
                          onKeyPress={ (event) => this.inputCode(event.nativeEvent.key, 6)}
                          onPaste={ (event) => this.inputCode(event.clipboardData.getData('Text'), 6) } />
                      </div>
                  </div>
              </div>

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
