import React from 'react'
import ReactDOM from 'react-dom'
import { browserHistory } from 'react-router'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'
import { InvalidMessageBox } from './elements/invalid_message_box'
import { range } from '../actions/common_actions'
import { _checkValue } from '../actions/validate_actions'

export default class UserName extends React.Component {

  componentDidMount() {
    ReactDOM.findDOMNode(this.refs.update_email_flg).checked = "checked"
  }

  componentWillReceiveProps(nextProps) {
    if(nextProps.user_name.to_next_page) {
      browserHistory.push(nextProps.user_name.to_next_page)
    }
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

  render() {
    let birthday_error_message = ''

    if(this.props.validate.birth_year.invalid) {
      birthday_error_message = this.props.validate.birth_year.message
    } else if(this.props.validate.birth_month.invalid) {
      birthday_error_message = this.props.validate.birth_month.message
    } else if(this.props.validate.birth_day.invalid) {
      birthday_error_message = this.props.validate.birth_day.message
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
                    <div className={(this.props.validate.first_name.invalid) ? 'has-error' : ''}>
                        <input ref="first_name" name="first_name" className="form-control signup_input-design" type="text"
                               placeholder={__("eg. Harry")}
                               onChange={ (e) => this.props.dispatch(_checkValue(e.target)) } />
                    </div>
                    <InvalidMessageBox is_invalid={this.props.validate.first_name.invalid}
                                       message={this.props.validate.first_name.message} />

                    {/* Last name */}
                    <div className={(this.props.validate.last_name.invalid) ? 'has-error' : ''}>
                        <input ref="last_name" name="last_name" className="form-control signup_input-design"
                               placeholder={__("eg. Armstrong")} type="text"
                               onChange={ (e) => this.props.dispatch(_checkValue(e.target)) } />
                    </div>
                    <InvalidMessageBox is_invalid={this.props.validate.last_name.invalid}
                                       message={this.props.validate.last_name.message} />

                    {/* Allow Email from goalous check */}
                    <div className="signup-checkbox-email-flg">
                        <input type="checkbox" className="signup-checkbox-input" value="1" ref="update_email_flg" />
                        <div className="signup-privacy-policy-label">
                          {__("I want to receive news and updates by email from Goalous.")}
                        </div>
                    </div>

                    {/* Birthday*/}
                    {/* TODO:アプリ申請のため、一時的にrequiredを外す */}
                    <div className="panel-heading signup-itemtitle">{__("Birthday")}</div>
                    <div className="form-inline signup_inputs-inline">
                        {/* Birthday year */}
                        <span className={(this.props.validate.birth_year.invalid) ? 'has-error' : ''}>
                            <select className="form-control inline-fix" ref="birth_year" name="birth_year"
                                    onChange={ (e) => this.props.dispatch(_checkValue(e.target)) }>
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
                        <span className={(this.props.validate.birth_month.invalid) ? 'has-error' : ''}>
                            <select className="form-control inline-fix" ref="birth_month" name="birth_month"
                                    onChange={ (e) => this.props.dispatch(_checkValue(e.target)) }>
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
                        <span className={(this.props.validate.birth_day.invalid) ? 'has-error' : ''}>
                        <select className="form-control inline-fix" ref="birth_day" name="birth_day"
                                onChange={ (e) => this.props.dispatch(_checkValue(e.target)) }>
                           <option value=""></option>
                           {
                             range(1, 31).map( day => {
                               return <option value={day} key={day}>{day}</option>;
                             })
                           }
                        </select>
                        </span>
                    </div>

                    <InvalidMessageBox is_invalid={this.props.validate.birth_year.invalid || this.props.validate.birth_month.invalid || this.props.validate.birth_day.invalid}
                                       message={birthday_error_message} />

                    {/* Privacy policy check */}
                    <div className="signup-checkbox">
                        <div className={(this.props.validate.privacy_policy.invalid) ? 'has-error' : ''}>
                            <input type="checkbox" value="1" className="signup-checkbox-input" ref="privacy_policy" name="privacy_policy"
                                   onChange={ (e) => this.props.dispatch(_checkValue(e.target)) } />
                            <div className="signup-privacy-policy-label"
                                 dangerouslySetInnerHTML={{__html: __("I agree to %s and %s of Goalous.", '<a href="/terms" target="_blank" className="signup-privacy-policy-link">term</a><br />', '<a href="/privacy_policy" target="_blank" className="signup-privacy-policy-link">Privacy Policy</a>')}}>
                            </div>
                        </div>
                    </div>

                    <InvalidMessageBox is_invalid={this.props.validate.privacy_policy.invalid}
                                       message={this.props.validate.privacy_policy.message} />

                    {/* Alert message */}
                    { (() => { if(this.props.user_name.is_exception) {
                      return <AlertMessageBox message={ this.props.user_name.exception_message } />;
                    }})() }

                    {/* Submit button */}
                    { (() => {
                      {/* TODO:アプリ申請のため、一時的に誕生日のバリデーションを無効にする */}
                      {/*const can_submit = this.props.validate.first_name.invalid === false && this.props.validate.last_name.invalid === false && this.props.validate.birth_year.invalid === false && this.props.validate.birth_month.invalid === false && this.props.validate.birth_day.invalid === false && this.props.validate.privacy_policy.invalid === false && !this.props.user_name.checking_user_name*/}
                      const can_submit = this.props.validate.first_name.invalid === false && this.props.validate.last_name.invalid === false && this.props.validate.privacy_policy.invalid === false && !this.props.user_name.checking_user_name

                      if(can_submit) {
                        return <EnabledNextButton />;
                      } else {
                        return <DisabledNextButton loader={ this.props.user_name.checking_user_name } />;
                      }}
                    )() }
                </form>
            </div>
        </div>
      </div>
    )
  }
}