import React from 'react'
import ReactDOM from 'react-dom'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'
import { InvalidMessageBox } from './elements/invalid_message_box'
import { _checkValue } from '../actions/validate_actions'


export default class TeamName extends React.Component {

  getInputDomData() {
    return ReactDOM.findDOMNode(this.refs.team_name).value.trim()
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.postTeamName(this.getInputDomData())
  }

  handleOnChange(e) {
    const validate_result = _checkValue(e.target)

    if(validate_result.error) {
      this.props.invalid(validate_result.messages)
    } else {
      this.props.valid()
    }
  }

  render() {
    return (
      <div className="row">
          <div className="panel panel-default panel-signup">
              <div className="panel-heading signup-title">{__("Choose your team name.")}</div>
              <img src="/img/signup/team.png"  className="signup-header-image" />
              <div className="signup-description">{__("Create a name for your team. A team is a group that can share goals, actions and posts  with each other. People outside of the team can't access this information.")}</div>

              <form className="form-horizontal" method="post" acceptCharset="utf-8"
                    onSubmit={(e) => this.handleSubmit(e) } >

                  {/* Team name */}
                  <div className="panel-heading signup-itemtitle">{__("Team Name")}</div>
                  <div className={(this.props.team_name.invalid_messages.team_name) ? 'has-error' : ''}>
                    <input className="form-control signup_input-design"
                           type="text"
                           ref="team_name"
                           name="team_name"
                           placeholder={__("eg. Team Goalous")}
                           onChange={this.handleOnChange.bind(this)} />
                    <InvalidMessageBox is_invalid={this.props.team_name.team_name_is_invalid}
                                       message={this.props.team_name.invalid_messages.team_name} />
                  </div>

                  {/* Alert message */}
                  { (() => { if(this.props.team_name.is_exception) {
                    return <AlertMessageBox message={ this.props.team_name.exception_message } />;
                  }})() }

                  {/* Submit button */}
                  { (() => { if(this.props.team_name.submit_button_is_enabled) {
                    return <EnabledNextButton />;
                  } else {
                    return <DisabledNextButton loader={ this.props.team_name.checking_team_name } />;
                  }})() }
              </form>
          </div>
      </div>
    )
  }
}
