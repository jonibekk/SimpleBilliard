import React from 'react'
import ReactDOM from 'react-dom'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'
import { InvalidMessageBox } from './elements/invalid_message_box'
import { range } from '../actions/common_actions'

export default class UserName extends React.Component {

  getInputDomData() {
    return {
      first_name: ReactDOM.findDOMNode(this.refs.first_name).value.trim(),
      last_name: ReactDOM.findDOMNode(this.refs.last_name).value.trim(),
      birth_year: ReactDOM.findDOMNode(this.refs.birth_year).value.trim(),
      birth_month: ReactDOM.findDOMNode(this.refs.birth_month).value.trim(),
      birth_day: ReactDOM.findDOMNode(this.refs.birth_day).value.trim(),
      privacy: ReactDOM.findDOMNode(this.refs.privacy).checked,
      update_email_flg: ReactDOM.findDOMNode(this.refs.update_email_flg).checked
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.postUserName(this.getInputDomData())
  }

  render() {
    return (
      <div>
        <div className="row">
            <div className="panel panel-default panel-signup">
                <div className="panel-heading signup-title">{__("What's your name?")}</div>
                <div className="signup-description">{__("Your name will be displayed only in the team of Goalous.")}</div>

                <form className="form-horizontal" acceptCharset="utf-8"
                      onSubmit={ (e) => this.handleSubmit(e) }>

                    {/* First name */}
                    <div className="panel-heading signup-itemtitle">{__("Your name")}</div>
                    <div className={(this.props.user_name.invalid_messages.first_name) ? 'has-error' : ''}>
                      <input ref="first_name" className="form-control signup_input-design" type="text"
                             placeholder={__("eg. Harry")} required
                             onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} />
                    </div>
                    <InvalidMessageBox is_invalid={this.props.user_name.user_name_is_invalid}
                                       message={this.props.user_name.invalid_messages.first_name} />

                    {/* Last name */}
                    <div className={(this.props.user_name.invalid_messages.last_name) ? 'has-error' : ''}>
                      <input ref="last_name" className="form-control signup_input-design"
                             placeholder={__("eg. Armstrong")} type="text"
                             required
                             onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} />
                    </div>
                    <InvalidMessageBox is_invalid={this.props.user_name.user_name_is_invalid}
                                       message={this.props.user_name.invalid_messages.last_name} />

                    {/* Birthday*/}
                    <div className="panel-heading signup-itemtitle">{__("Birthday")}</div>
                    <div className="form-inline signup_inputs-inline">
                        {/* Birthday year */}
                        <select className="form-control inline-fix" ref="birth_year" required
                                onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} >
                           <option value=""></option>
                           {
                             range(1910, new Date().getFullYear()).sort((a,b) => b-a).map( year => {
                               return <option value={year} key={year}>{year}</option>;
                             })
                           }
                        </select>
                        &nbsp;/&nbsp;

                        {/* Birthday month */}
                        <select className="form-control inline-fix" ref="birth_month" required
                                onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} >
                           <option value=""></option>
                           <option value="01">{__("Jan")}</option>
                           <option value="02">{__("Feb")}</option>
                           <option value="03">{__("Mar")}</option>
                           <option value="04">{__("Apr")}</option>
                           <option value="05">{__("May")}</option>
                           <option value="06">{__("Jun")}</option>
                           <option value="07">{__("Jul")}</option>
                           <option value="08">{__("Aug")}</option>
                           <option value="09">{__("Sep")}</option>
                           <option value="10">{__("Oct")}</option>
                           <option value="11">{__("Nov")}</option>
                           <option value="12">{__("Dec")}</option>
                        </select>
                        &nbsp;/&nbsp;

                        {/* Birthday day */}
                        <select className="form-control inline-fix" ref="birth_day"
                                required
                                onChange={ () => { this.props.inputUserName(this.getInputDomData()) }} >
                           <option value=""></option>
                           {
                             range(1, 31).map( day => {
                               return <option value={day} key={day}>{day}</option>;
                             })
                           }
                        </select>

                        <InvalidMessageBox is_invalid={this.props.user_name.user_name_is_invalid}
                                           message={this.props.user_name.invalid_messages.birth_day} />
                    </div>

                    {/* Allow Email from goalous check */}
                    <div className="checkbox signup-checkbox">
                        <input type="checkbox" value="1" className="signup-checkbox" ref="update_email_flg"
                               onChange={ () => { this.props.inputUserName(this.getInputDomData()) } } />
                        <div className="signup-privacy-policy-label">
                          {__("It's ok to send me email about Goalous.")}
                        </div>
                    </div>

                    {/* Privacy policy check */}
                    <div className="checkbox signup-checkbox">
                        <input type="checkbox" value="1" className="signup-checkbox" ref="privacy"
                               onChange={ () => { this.props.inputUserName(this.getInputDomData()) } } />
                        <div className="signup-privacy-policy-label" dangerouslySetInnerHTML={{__html: __("I agree to %s and %s of Goalous.", '<a href="/terms" target="_blank" className="signup-privacy-policy-link">term</a><br />', '<a href="/privacy_policy" target="_blank" className="signup-privacy-policy-link">Privacy Policy</a>')}}>
                        </div>
                    </div>

                    {/* Alert message */}
                    { (() => { if(this.props.user_name.is_exception) {
                      return <AlertMessageBox message={ this.props.user_name.exception_message } />;
                    }})() }

                    {/* Submit button */}
                    { (() => { if(this.props.user_name.submit_button_is_enabled) {
                      return <EnabledNextButton />;
                    } else {
                      return <DisabledNextButton loader={ this.props.user_name.checking_user_name } />;
                    }})() }

                </form>
            </div>
        </div>
      </div>
    )
  }
}
