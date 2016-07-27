import React from 'react'
import ReactDOM from 'react-dom'
import { Link } from 'react-router'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'

export default class UserName extends React.Component {

  getInputDomData() {
    return {
      first_name: ReactDOM.findDOMNode(this.refs.first_name).value.trim(),
      last_name: ReactDOM.findDOMNode(this.refs.last_name).value.trim(),
      privacy: ReactDOM.findDOMNode(this.refs.privacy).checked
    }
  }

  render() {
    const disabled_btn = () => {
      return (
        <DisabledNextButton loader={this.props.user_name.checking_user_name} />
      )
    }
    const enabled_btn = () => {
      return (
        <EnabledNextButton onSubmit={() => this.props.postUserName(this.getInputDomData()) } />
      )
    }
    const exception_message_box = () => {
      return (
        <div className="signup-error-description">
            <i className="fa fa-exclamation-circle signup-load-icon mod-error"></i> {this.props.user_name.exception_message}
        </div>
      )
    }

    return (
      <div>
        <div className="row">
            {/*<!-- START app/View/Signup/user.ctp -->*/}
            <div className="panel panel-default panel-signup">
                <div className="panel-heading signup-title">{"What's your name?"}</div>
                <div className="signup-description">username sample textusername sample text username sample text username sample text username sample text username sample text.</div>

                <form action="#" className="form-horizontal" id="" acceptCharset="utf-8">
                    <div className="panel-heading signup-itemtitle">your name</div>
                    <input ref="first_name" className="form-control signup_input-design" placeholder="例) Suzuki" type="text"
                           onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} />
                    <input ref="last_name" className="form-control signup_input-design" placeholder="例) Hanako" type="text"
                           onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} />
                    <div className="checkbox signup-checkbox">
                        <input type="hidden" name="data[User][agree_tos]" id="UserAgreeTos_" value="0" />
                        <label>
                            <input type="checkbox" value="1" id="UserAgreeTos" ref="privacy"
                                   onChange={ () => { this.props.inputUserName(this.getInputDomData()) } } /> Goalousの<Link to="/terms" target="_blank" className="link">利用規約</Link>と<Link to="/privacy_policy" target="_blank" className="link">プライバシーポリシー</Link>に同意します。</label>
                    </div>
                    { this.props.user_name.is_exception ? exception_message_box() : '' }
                    { this.props.user_name.submit_button_is_enabled ? enabled_btn() : disabled_btn() }
                </form>
            </div>
        </div>
        {/*<!-- END app/View/Signup/user.ctp -->*/}
      </div>
    )
  }
}
