import React from 'react'
import ReactDOM from 'react-dom'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'
import { InvalidMessageBox } from './elements/invalid_message_box'
import { range } from '../actions/common_actions'
import { _checkValue } from '../actions/validate_actions'
import { dispatch } from 'redux'

export default class UserName extends React.Component {
  componentDidMount() {
    ReactDOM.findDOMNode(this.refs.update_email_flg).checked = "checked"
  }

  getInputDomData() {
    return {
      first_name: ReactDOM.findDOMNode(this.refs.first_name).value.trim(),
      last_name: ReactDOM.findDOMNode(this.refs.last_name).value.trim(),
      birth_year: ReactDOM.findDOMNode(this.refs.birth_year).value.trim(),
      birth_month: ReactDOM.findDOMNode(this.refs.birth_month).value.trim(),
      birth_day: ReactDOM.findDOMNode(this.refs.birth_day).value.trim(),
      privacy_policy: ReactDOM.findDOMNode(this.refs.privacy_policy).checked,
      update_email_flg: ReactDOM.findDOMNode(this.refs.update_email_flg).checked
    }
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.postUserName(this.getInputDomData())
  }

  handleOnChange(e) {
    this._sendValidateState(_checkValue(e.target))
  }

  _sendValidateState(status) {
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
    let birthday_error_message = ''

    if(this.props.user_name.invalid_messages.birth_year) {
      birthday_error_message = this.props.user_name.invalid_messages.birth_year
    } else if(this.props.user_name.invalid_messages.birth_month) {
      birthday_error_message = this.props.user_name.invalid_messages.birth_month
    } else if(this.props.user_name.invalid_messages.birth_day) {
      birthday_error_message = this.props.user_name.invalid_messages.birth_day
    }
    return (
      <div>
        <div className="row">
            <div className="panel panel-default panel-signup">
                <div className="panel-heading signup-title">{__("What's your name?")}</div>
                <img src="/img/signup/user.png" className="signup-header-image" />
                <div className="signup-description">{__("Your name will only be displayed to your team on Goalous.")}</div>

                <form className="form-horizontal" acceptCharset="utf-8"
                      onSubmit={ (e) => this.handleSubmit(e) }>

                    {/* First name */}
                    <div className="panel-heading signup-itemtitle">{__("Your name")}</div>
                    <div className={(this.props.user_name.invalid.first_name) ? 'has-error' : ''}>
                        <input ref="first_name" name="first_name" className="form-control signup_input-design" type="text"
                               placeholder={__("eg. Harry")}
                               onChange={this.handleOnChange.bind(this)} />
                    </div>
                    <InvalidMessageBox is_invalid={this.props.user_name.invalid.first_name}
                                       message={this.props.user_name.invalid_messages.first_name} />

                    {/* Last name */}
                    <div className={(this.props.user_name.invalid.last_name) ? 'has-error' : ''}>
                        <input ref="last_name" name="last_name" className="form-control signup_input-design"
                               placeholder={__("eg. Armstrong")} type="text"
                               onChange={this.handleOnChange.bind(this)} />
                    </div>
                    <InvalidMessageBox is_invalid={this.props.user_name.invalid.last_name}
                                       message={this.props.user_name.invalid_messages.last_name} />

                    {/* Allow Email from goalous check */}
                    <div className="signup-checkbox-email-flg">
                        <input type="checkbox" className="signup-checkbox-input" value="1" ref="update_email_flg" />
                        <div className="signup-privacy-policy-label">
                          {__("I want to receive news and updates by email from Goalous.")}
                        </div>
                    </div>

                    {/* Birthday*/}
                    <div className="panel-heading signup-itemtitle">{__("Birthday")}</div>
                    <div className="form-inline signup_inputs-inline">
                        {/* Birthday year */}
                        <span className={(this.props.user_name.invalid.birth_year) ? 'has-error' : ''}>
                            <select className="form-control inline-fix" ref="birth_year" name="birth_year" required
                                    onChange={this.handleOnChange.bind(this)}>
                               <option value=""></option>
                               {
                                 range(1910, new Date().getFullYear()).sort((a,b) => b-a).map( year => {
                                   return <option value={year} key={year}>{year}</option>;
                                 })
                               }
                            </select>
                        </span>
                        &nbsp;/&nbsp;

                        {/* Birthday month */}
                        <span className={(this.props.user_name.invalid.birth_month) ? 'has-error' : ''}>
                            <select className="form-control inline-fix" ref="birth_month" name="birth_month" required
                                    onChange={this.handleOnChange.bind(this)}>
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
                        </span>
                        &nbsp;/&nbsp;

                        {/* Birthday day */}
                        <span className={(this.props.user_name.invalid.birth_day) ? 'has-error' : ''}>
                        <select className="form-control inline-fix" ref="birth_day" name="birth_day" required
                                onChange={this.handleOnChange.bind(this)}>
                           <option value=""></option>
                           {
                             range(1, 31).map( day => {
                               return <option value={day} key={day}>{day}</option>;
                             })
                           }
                        </select>
                        </span>
                    </div>

                    <InvalidMessageBox is_invalid={this.props.user_name.invalid.birth_year || this.props.user_name.invalid.birth_month || this.props.user_name.invalid.birth_day}
                                       message={birthday_error_message} />

                    {/* Privacy policy check */}
                    <div className="signup-checkbox">
                        <div className={(this.props.user_name.invalid.privacy_policy) ? 'has-error' : ''}>
                            <input type="checkbox" value="1" className="signup-checkbox-input" ref="privacy_policy" name="privacy_policy"
                                   onChange={this.handleOnChange.bind(this)} />
                            <div className="signup-privacy-policy-label"
                                 dangerouslySetInnerHTML={{__html: __("I agree to %s and %s of Goalous.", '<a href="/terms" target="_blank" className="signup-privacy-policy-link">term</a><br />', '<a href="/privacy_policy" target="_blank" className="signup-privacy-policy-link">Privacy Policy</a>')}}>
                            </div>
                        </div>
                    </div>

                    <InvalidMessageBox is_invalid={this.props.user_name.invalid.privacy_policy}
                                       message={this.props.user_name.invalid_messages.privacy_policy} />

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
