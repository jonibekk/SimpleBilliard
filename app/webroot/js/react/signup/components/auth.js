import React from 'react'
import ReactDOM from 'react-dom'
import { range } from '../actions/common_actions'
import { browserHistory } from 'react-router'

export default class Auth extends React.Component {
  constructor(props) {
    super(props)
  }

  inputCode(inputed_code, index) {

    // 確認中の場合も何もしない
    if(this.props.auth.checking_auth_code) {
      return
    }

    if(inputed_code === '') {
      return
    }

    this.props.inputCode(index, inputed_code)

    // inputフィールドのフォーカス変更
    const key_name = `code${index + 1}`

    if(index < 6) {
      ReactDOM.findDOMNode(this.refs[key_name]).focus()
      ReactDOM.findDOMNode(this.refs[key_name]).select()
    } else {
      ReactDOM.findDOMNode(this.refs.code1).focus()
      ReactDOM.findDOMNode(this.refs.code1).select()
    }
  }

  pasteCode(pasted_code, index) {
    const code_splitted = pasted_code.replace( /-/g , "").split("")

    code_splitted.map((code, i) => {
      const target_index = index + i

      if(target_index > 6) {
        return
      }

      this.props.inputCode(target_index, code)
      ReactDOM.findDOMNode(this.refs[`code${target_index}`]).value = code

      if(index === 6) {
        ReactDOM.findDOMNode(this.refs.code1).focus()
        ReactDOM.findDOMNode(this.refs.code1).select()
      }
    })
  }

  handleKeyDown(key_code, index) {
    const code_index = `code${index}`
    const previous_index = parseInt(index) - 1
    const previous_code_index = `code${previous_index}`

    if(previous_index < 1) {
      return
    }

    if(key_code === 8) {
      if(this.props.auth.code_list[code_index] === '') {
        ReactDOM.findDOMNode(this.refs[previous_code_index]).focus()
        ReactDOM.findDOMNode(this.refs[previous_code_index]).select()
        ReactDOM.findDOMNode(this.refs[previous_code_index]).value = ''
        this.props.inputCode(previous_index, "")
      } else {
        this.props.inputCode(index, "")
      }
    }
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.auth.to_next_page) {
      browserHistory.push(nextProps.auth.to_next_page)
    }

    for(const key in nextProps.auth.code_list) {
      if(nextProps.auth.code_list[key]) {
        return
      }
    }

    // フォームをリセット
    range(1, 7).map(index => {
      ReactDOM.findDOMNode(this.refs[`code${index}`]).value = ''
    })
  }

  render() {
    const checking_box = () => {
      return (
        <div className="signup-load-description">
            <div className="signup-load-icon">
                <img src="/img/ajax-loader.gif" />
            </div>
            loading...
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
              <div className="panel-heading signup-title">{__("Check your email!")}</div>
              <div className="signup-description">{__("We've sent a six-digit confirmation code to %s. Enter it below to confirm your email address.")}</div>

              <div className="signup-auth-split_input signup-auth-large_margin">
                  <div className="confirmation_code_group">
                    {
                      range(1, 4).map(index => {
                        return (
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper" key={`code${index}`}>
                              <input type="text" className="signup-auth-inline_input" ref={`code${index}`} maxLength="1" pattern="\d*"
                                     onChange={ event => {this.inputCode(event.target.value, index)}}
                                     onKeyDown={ event => { this.handleKeyDown(event.keyCode, index) } }
                                     onPaste={ event => {
                                       this.pasteCode(event.clipboardData.getData('Text'), index)
                                     }} />
                          </div>
                        )
                      })
                    }
                  </div>

                  <div className="signup-auth-confirmation_code_span_cell">—</div>

                  <div className="confirmation_code_group">
                    {
                      range(4, 7).map(index => {
                        return (
                          <div className="signup-auth-split_input_item signup-auth-input_wrapper" key={`code${index}`}>
                              <input type="text" className="signup-auth-inline_input" ref={`code${index}`} maxLength="1" pattern="\d*"
                                     onChange={ event => {this.inputCode(event.target.value, index)}}
                                     onKeyDown={ event => { this.handleKeyDown(event.keyCode, index) } }
                                     onPaste={ event => {
                                       this.pasteCode(event.clipboardData.getData('Text'), index)
                                     }} />
                          </div>
                        )
                      })
                    }
                  </div>
              </div>

              {this.props.auth.checking_auth_code ? checking_box() : ''}
              {this.props.auth.auth_code_is_invalid ? alert_box() : ''}
              {this.props.auth.auth_code_is_locked || this.props.auth.auth_code_is_expired ? locked_box() : ''}

              <div className="signup-description">{__("Please don't close this window.")}</div>
          </div>
      </div>
    )
  }
}
