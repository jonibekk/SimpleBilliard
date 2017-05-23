import React from 'react'
import ReactDOM from 'react-dom'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'
import { InvalidMessageBox } from './elements/invalid_message_box'
import { _checkValue } from '../actions/validate_actions'
import { generateCurrentRangeList } from '~/util/date'

export default class Term extends React.Component {

  constructor(props) {
    super(props)

    this.state = {
      term_length: null
    }
  }

  getInputDomData() {
    return {
      next_start_ym: ReactDOM.findDOMNode(this.refs.next_start_ym).value.trim(),
      term: ReactDOM.findDOMNode(this.refs.term).value.trim(),
      timezone: ReactDOM.findDOMNode(this.refs.timezone).value.trim()
    }
  }

  handleOnChange(e) {
    const res = _checkValue(e.target)

    if (e.target.name === 'next_start_ym') {
      this.props.setNextRangeList(this.getInputDomData().next_start_ym)
    }
    if (e.target.name === 'term') {
      this.setState({term_length: this.getInputDomData().term})
    }
    this.props.dispatch(res)
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.postTerms(this.getInputDomData())
  }

  render() {
    return (
      <div className="row">
          <div className="panel panel-default panel-signup">
              <div className="panel-heading signup-title">{__("Choose your team's (company's) term")}</div>
              <img src="/img/signup/term.png" className="signup-header-image" />
              <div className="signup-description">{__("The term can be based on your corporate / financial calendar, personal evaluations or any period of time the works best for your company.Choose the months that start and end your first two terms.")}</div>

              <form className="form-horizontal" acceptCharset="utf-8"
                    onSubmit={ this.handleSubmit.bind(this) } >

                  {/* current term */}
                  <div className="panel-heading signup-itemtitle">{__("Current Term")}</div>
                  <div className={(this.props.validate.next_start_ym.invalid) ? 'has-error' : ''}>
                      <select className="form-control signup_input-design" ref="next_start_ym" name="next_start_ym"
                              onChange={ this.handleOnChange.bind(this) }>
                          <option value="">{__("Please select")}</option>
                          { generateCurrentRangeList().map((option) => {
                            return (
                              <option value={option.next_start_ym} key={option.next_start_ym}>{option.range}</option>
                            )
                          })}
                      </select>
                  </div>
                  <InvalidMessageBox is_invalid={this.props.validate.next_start_ym.invalid}
                                     message={this.props.validate.next_start_ym.message} />

                  {/* next term */}
                  <div className="panel-heading signup-itemtitle">{__("Next Term")}</div>

                  <div className={(this.props.validate.term.invalid) ? 'has-error' : ''}>
                      <select className="form-control signup_input-design" ref="term" name="term"
                              onChange={ this.handleOnChange.bind(this) }>
                          <option value="">{__("Please select")}</option>
                          {
                            this.props.term.next_range_list.map((option) => {
                              return (
                                <option value={option.term_length} key={option.term_length}>{option.range}</option>
                              )
                            })
                          }
                      </select>
                  </div>
                  <InvalidMessageBox is_invalid={this.props.validate.term.invalid}
                                     message={this.props.validate.term.message} />

                  <div className="signup-term-description">
                    {!this.props.validate.term.invalid && <p>{__(`The default length of any future terms are automatically set to ${this.state.term_length} months.`)}</p>}
                    <p>{__("You can change this setting at any time.")}</p>
                  </div>

                  {/* Timezone */}
                  <div className="panel-heading signup-itemtitle">{__("Timezone")}</div>
                  { (() => { if(this.props.term.is_timezone_edit_mode) {
                    const timezone_options = []

                    for(const key in cake.data.timezones) {
                      timezone_options.push(
                        <option value={key} key={cake.data.timezones[key]}>{cake.data.timezones[key]}</option>
                      )
                    }
                    return (
                      <select className="form-control signup_input-design" defaultValue="+9.0" ref="timezone">
                        {timezone_options}
                      </select>
                    )
                  } else {
                    return (
                      <p className="signup-timezone-label-wrapper">
                          <span className="signup-goal-timezone-label">(GMT+9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</span>
                          <a href="#" onClick={ () => { this.props.changeToTimezoneSelectMode() } }>{__("Change")}</a>
                          <select ref="timezone" className="none" defaultValue="+9.0">
                            <option value="+9.0">{cake.data.timezones["+9.0"]}</option>
                          </select>
                      </p>
                    )
                  }})() }

                  {/* Alert message */}
                  { (() => { if(this.props.term.is_exception) {
                    return <AlertMessageBox message={ this.props.term.exception_message } />;
                  }})() }

                  {/* Submit button */}
                  { (() => {
                    const can_submit = this.props.validate.term.invalid === false && this.props.validate.next_start_ym.invalid === false && !this.props.term.checking_term

                    if(can_submit) {
                      return <EnabledNextButton />;
                    } else {
                      return <DisabledNextButton loader={ this.props.term.checking_term } />;
                    }}
                  )() }

              </form>

          </div>
      </div>
    )
  }
}
