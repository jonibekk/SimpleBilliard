import React from 'react'
import ReactDOM from 'react-dom'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'
import { InvalidMessageBox } from './elements/invalid_message_box'
import { _checkValue } from '../actions/common_actions'

export default class Password extends React.Component {

  getInputDomData() {
    return ReactDOM.findDOMNode(this.refs.password).value.trim()
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.postPassword(this.getInputDomData())
  }

  handleOnChange(e) {
    const status = _checkValue(e.target)
    const element = { invalid: {}, messages: {} }

    if(status.error) {
      element.invalid[status.name] = true
      element.messages = status.messages
      this.props.invalid(element)
    } else {
      element.invalid[status.name] = false
      element.messages[status.name] = ''
      this.props.valid(element)
    }
  }

  render() {
    return (
      <div className="row">
          <div className="panel panel-default panel-signup">
              <div className="panel-heading signup-title">{__("Set your password")}</div>
              <img src="/img/signup/password.png"  className="signup-header-image" />
              <div className="signup-description">{__("Create your password to login to Goalous.")}</div>

              <form className="form-horizontal" method="post" acceptCharset="utf-8"
                    onSubmit={(e) => this.handleSubmit(e) } >

                  {/* Password */}
                  <div className="panel-heading signup-itemtitle">{__("Password")}</div>
                  <div className={(this.props.password.invalid.password) ? 'has-error' : ''}>
                    <input className="form-control signup_input-design"
                           placeholder="********"
                           type="password"
                           ref="password"
                           name="password"
                           required
                           onChange={this.handleOnChange.bind(this)} />
                  </div>
                  <InvalidMessageBox is_invalid={this.props.password.invalid.password}
                                     message={this.props.password.invalid_messages.password} />
                  <div className="signup-description mod-small">{__("Use 8 or more characters including at least one number.")}</div>

                  {/* Alert message */}
                  { (() => { if(this.props.password.is_exception) {
                    return <AlertMessageBox message={ this.props.password.exception_message } />;
                  }})() }

                  {/* Submit button */}
                  { (() => { if(this.props.password.submit_button_is_enabled) {
                    return <EnabledNextButton />;
                  } else {
                    return <DisabledNextButton loader={ this.props.password.checking_password } />;
                  }})() }

              </form>
          </div>
      </div>
    )
  }
}
