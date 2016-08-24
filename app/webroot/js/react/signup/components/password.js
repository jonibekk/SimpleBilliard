import React from 'react'
import ReactDOM from 'react-dom'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'
import { InvalidMessageBox } from './elements/invalid_message_box'
import validator from 'validator'

export default class Password extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      validate: {
        password: {
          pattern: /^(?=.*?[0-9])(?=.*?[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{8,50}$/,
          message: __("Password is incorrect.")
        }
      }
    }
  }

  getInputDomData() {
    return ReactDOM.findDOMNode(this.refs.password).value.trim()
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.postPassword(this.getInputDomData())
  }

  _checkValue(event) {
    const val = event.target.value

    if(validator.matches(val, this.state.validate.password.pattern)) {
      this.props.valid()
    } else {
      this.props.invalid({password: this.state.validate.password.message})
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
