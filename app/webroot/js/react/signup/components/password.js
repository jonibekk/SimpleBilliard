import React from 'react'
import ReactDOM from 'react-dom'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'
import { InvalidMessageBox } from './elements/invalid_message_box'
import { getValidateError } from '../actions/common_actions'

export default class Password extends React.Component {

  getInputDomData() {
    return ReactDOM.findDOMNode(this.refs.password).value.trim()
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.postPassword(this.getInputDomData())
  }

  _checkValue(event) {
    const errors = getValidateError(event.target)

    if(errors === null) return
    if(errors !== {}) {
      this.props.invalid({password: errors.password})
    } else {
      this.props.valid()
    }

    // () => { this.props.inputPassword(this.getInputDomData()) }
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
                  <div className={(this.props.password.invalid_messages.password) ? 'has-error' : ''}>
                    <input className="form-control signup_input-design"
                           placeholder="********"
                           maxLength="50"
                           type="password"
                           ref="password"
                           name="password"
                           required
                           onChange={this._checkValue.bind(this)} />
                  </div>
                  <InvalidMessageBox is_invalid={this.props.password.password_is_invalid}
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
