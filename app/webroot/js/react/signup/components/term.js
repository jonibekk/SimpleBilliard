import React from 'react'
import ReactDOM from 'react-dom'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'
import { InvalidMessageBox } from './elements/invalid_message_box'
import { _checkValue } from '../actions/validate_actions'

export default class Term extends React.Component {

  getInputDomData(ref_name) {
    return ReactDOM.findDOMNode(this.refs[ref_name]).value.trim()
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.postTerms()
  }

  render() {
    return (
      <div className="row">
          <div className="panel panel-default panel-signup">
              <div className="panel-heading signup-title">{__("Choose your team's (company's) term")}</div>
              <img src="/img/signup/term.png" className="signup-header-image" />
              <div className="signup-description">{__("Set the term for your team. The term can be based on your corporate / financial calendar, personal evaluations or any period of time the works best for your company.")}</div>

              <form className="form-horizontal" acceptCharset="utf-8"
                    onSubmit={(e) => this.handleSubmit(e) } >

                  {/* Term */}
                  <div className="panel-heading signup-itemtitle">{__("Term")}</div>
                  <div className={(this.props.term.invalid.term) ? 'has-error' : ''}>
                      <select className="form-control signup_input-design" ref="term" onChange={ () => { this.props.selectTerm(this.getInputDomData('term')) } }>
                          <option value="">{__("Please select")}</option>
                          <option value="3">{__("Quater")}</option>
                          <option value="6">{__("Half a year")}</option>
                          <option value="12">{__("Year")}</option>
                      </select>
                  </div>

                  <InvalidMessageBox is_invalid={this.props.term.invalid.term}
                                     message={this.props.term.invalid_messages.term} />

                  {/* Start month */}
                  <div className="panel-heading signup-itemtitle">{__("Select your present term")}</div>
                  { (() => { if(this.props.term.selected_term) {
                    return (
                      <div className={(this.props.term.invalid.start_month) ? 'has-error' : ''}>
                          <select className="form-control signup_input-design" ref="start_month" onChange={ () => { this.props.selectStartMonth(this.getInputDomData('start_month')) } }>
                              <option value="">{__("Please select")}</option>
                              {
                                this.props.term.start_month_list.map((option) => {
                                  return (
                                    <option value={option.start_month} key={option.range}>{option.range}</option>
                                  )
                                })
                              }
                          </select>
                      </div>
                    )
                  } else {
                    return (
                      <select className="form-control signup_input-design">
                        <option value="">{__("Please select")}</option>
                      </select>
                    )
                  }})() }

                  <InvalidMessageBox is_invalid={this.props.term.invalid.start_month}
                                     message={this.props.term.invalid_messages.start_month} />

                  {/* Timezone */}
                  <div className="panel-heading signup-itemtitle">{__("Timezone")}</div>
                  { (() => { if(this.props.term.is_timezone_edit_mode) {
                    let timezone_options = []

                    for(const key in cake.data.timezones) {
                      timezone_options.push(
                        <option value={key} key={cake.data.timezones[key]}>{cake.data.timezones[key]}</option>
                      )
                    }
                    return (
                      <select className="form-control signup_input-design" defaultValue="+9.0" ref="timezone"
                              onChange={ () => { this.props.selectTimezone(this.getInputDomData('timezone')) } }>
                        {timezone_options}
                      </select>
                    )
                  } else {
                    return (
                      <p className="signup-timezone-label-wrapper">
                          <span className="signup-goal-timezone-label">(GMT+9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</span>
                          <a href="#" onClick={ () => { this.props.changeToTimezoneSelectMode() } }>{__("Change")}</a>
                      </p>
                    )
                  }})() }

                  {/* Alert message */}
                  { (() => { if(this.props.term.is_exception) {
                    return <AlertMessageBox message={ this.props.term.exception_message } />;
                  }})() }

                  {/* Submit button */}
                  { (() => { if(this.props.term.submit_button_is_enabled) {
                    return <EnabledNextButton />;
                  } else {
                    return <DisabledNextButton loader={ this.props.term.checking_term } />;
                  }})() }

              </form>

          </div>
      </div>
    )
  }
}
