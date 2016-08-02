import React from 'react'
import ReactDOM from 'react-dom'

export default class Auth extends React.Component {
  constructor(props) {
    super(props)
  }

  inputCode(inputed_code, index) {

    // 確認中の場合も何もしない
    if(this.props.auth.checking_auth_code) {
      return
    }

    // inputフィールドのフォーカス変更
    const key_name = `code${index + 1}`

    if(index < 6) {
      ReactDOM.findDOMNode(this.refs[key_name]).select()
      ReactDOM.findDOMNode(this.refs[key_name]).focus()
    } else {
      ReactDOM.findDOMNode(this.refs.code1).select()
      ReactDOM.findDOMNode(this.refs.code1).focus()
    }
  }

  pasteCode(pasted_code, index) {
    const code_splitted = pasted_code.split("").replace( /-/g , "")

    code_splitted.map((code, i) => {
      const target_index = index + i

      if(code === '-') {
        return
      }

      if(target_index > 7) {
        return
      }

      this.props.inputCode(target_index, code)
    })

    // inputフィールドのフォーカス変更
    const key_name = `code${index + 1}`

    if(index < 6) {
      ReactDOM.findDOMNode(this.refs[key_name]).select()
      ReactDOM.findDOMNode(this.refs[key_name]).focus()
    } else {
      ReactDOM.findDOMNode(this.refs.code1).select()
      ReactDOM.findDOMNode(this.refs.code1).focus()
    }
  }

  handleKeyDown(key_code, index) {
    const code_index = `code${index}`
    const previous_index = parseInt(index) - 1
    const previous_code_index = `code${previous_index}`

    if(previous_index < 1) {
      return
    }

    if(key_code === 8) {
      if(String(this.props.auth.code_list[code_index]).length === 0) {
        ReactDOM.findDOMNode(this.refs[previous_code_index]).select()
        ReactDOM.findDOMNode(this.refs[previous_code_index]).focus()
      }
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
      <div className="row">
          <div className="panel panel-default panel-signup">
              <div className="panel-heading signup-title">Check your email!</div>
              <div className="signup-description">Auth code sample text.Auth code sample textAuth code sample textAuth code sample textAuth code sample textAuth code sample text.</div>

              <div className="signup-auth-split_input signup-auth-large_margin" data-multi-input-code="true">
                  <div className="confirmation_code_group">
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code1" maxLength="1" type="text"
                                 onChange={ (event) => {this.inputCode(event.target.value, 1)}}
                                 onPaste={ (event) => this.pasteCode(event.clipboardData.getData('Text'), 1) } />
                      </div>
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code2" maxLength="1" type="text"
                                 onChange={ (event) => this.inputCode(event.target.value, 2)}
                                 onPaste={ (event) => this.pasteCode(event.clipboardData.getData('Text'), 2) }
                                 onKeyDown={ (event) => { this.handleKeyDown(event.keyCode, 2) } } />
                      </div>
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code3" maxLength="1" type="text"
                                 onChange={ (event) => this.inputCode(event.target.value, 3)}
                                 onPaste={ (event) => this.pasteCode(event.clipboardData.getData('Text'), 3) }
                                 onKeyDown={ (event) => { this.handleKeyDown(event.keyCode, 3) } } />
                      </div>
                  </div>

                  <div className="signup-auth-confirmation_code_span_cell">—</div>

                  <div className="confirmation_code_group">
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code4" maxLength="1" type="text"
                                 onChange={ (event) => this.inputCode(event.target.value, 4)}
                                 onPaste={ (event) => this.pasteCode(event.clipboardData.getData('Text'), 4) }
                                 onKeyDown={ (event) => { this.handleKeyDown(event.keyCode, 4) } } />
                      </div>
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code5" maxLength="1" type="text"
                                 onChange={ (event) => this.inputCode(event.target.value, 5)}
                                 onPaste={ (event) => this.pasteCode(event.clipboardData.getData('Text'), 5) }
                                 onKeyDown={ (event) => { this.handleKeyDown(event.keyCode, 5) } } />
                      </div>
                      <div className="signup-auth-split_input_item signup-auth-input_wrapper">
                          <input type="text" className="signup-auth-inline_input" ref="code6" maxLength="1" type="text"
                                 onChange={ (event) => this.inputCode(event.target.value, 6)}
                                 onPaste={ (event) => this.pasteCode(event.clipboardData.getData('Text'), 6) }
                                 onKeyDown={ (event) => { this.handleKeyDown(event.keyCode, 6) } } />
                      </div>
                  </div>
              </div>

              {this.props.auth.checking_auth_code ? checking_box() : ''}
              {this.props.auth.auth_code_is_invalid ? alert_box() : ''}
              {this.props.auth.auth_code_is_locked || this.props.auth.auth_code_is_expired ? locked_box() : ''}

              <div className="signup-description">Auth code sample text.Auth code sample textAuth code sample textAuth code sample textAuth code sample textAuth code sample text.</div>
          </div>
      </div>
    )
  }
}
