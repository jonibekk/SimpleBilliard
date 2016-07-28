import React from 'react'
import ReactDOM from 'react-dom'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'
import { InvalidMessageBox } from './elements/invalid_message_box'

export default class Password extends React.Component {

  getInputDomData() {
    return ReactDOM.findDOMNode(this.refs.password).value.trim()
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.postPassword(this.getInputDomData())
  }

  render() {
    return (
      <div className="row">
          <div className="panel panel-default panel-signup">
              <div className="panel-heading signup-title">Set your password</div>
              <div className="signup-description">password sample text password sample text password sample text.</div>

              <form className="form-horizontal" method="post" acceptCharset="utf-8"
                    onSubmit={(e) => this.handleSubmit(e) } >

                  {/* Password */}
                  <div className="panel-heading signup-itemtitle">Password</div>
                  <div className={(this.props.password.invalid_messages.password) ? 'has-error' : ''}>
                    <input className="form-control signup_input-design" placeholder="8文字以上。 アルファベット文字、数字が混在している必要があります。" maxLength="50" type="password" ref="password"
                           onChange={ () => { this.props.inputPassword(this.getInputDomData()) }} />
                  </div>
                  <InvalidMessageBox is_invalid={this.props.password.password_is_invalid}
                                     message={this.props.password.invalid_messages.password} />

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
