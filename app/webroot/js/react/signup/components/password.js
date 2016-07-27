import React from 'react'
import ReactDOM from 'react-dom'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'

export default class Password extends React.Component {
  getInputDomData() {
    return ReactDOM.findDOMNode(this.refs.password).value.trim()
  }
  render() {
    const disabled_btn = () => {
      return (
        <DisabledNextButton loader={this.props.password.checking_password} />
      )
    }
    const enabled_btn = () => {
      return (
        <EnabledNextButton onSubmit={() => this.props.postPassword(this.getInputDomData()) } />
      )
    }
    const exception_message_box = () => {
      return (
        <AlertMessageBox message={this.props.password.exception_message} />
      )
    }

    return (
      <div className="row">
          {/* <!-- START app/View/Signup/password.ctp --> */}
          <div className="panel panel-default panel-signup">
              <div className="panel-heading signup-title">Set your password</div>
              <div className="signup-description">password sample text password sample text password sample text.</div>

              <form action="#" className="form-horizontal" id="" method="post" acceptCharset="utf-8">
                  <div className="panel-heading signup-itemtitle">Password</div>
                  <input className="form-control signup_input-design" placeholder="********" maxLength="50" type="password" ref="password"
                         onChange={ () => { this.props.inputPassword(this.getInputDomData()) }} />
                  <div className="signup-description mod-small">8文字以上。 アルファベット大文字、小文字、数字が混在している必要があります。記号は使えません。</div>

                  { this.props.password.is_exception ? exception_message_box() : '' }
                  { this.props.password.submit_button_is_enabled ? enabled_btn() : disabled_btn() }
              </form>
          </div>
          {/* <!-- END app/View/Signup/password.ctp --> */}
      </div>
    )
  }
}
