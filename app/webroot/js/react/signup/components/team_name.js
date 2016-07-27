import React from 'react'
import ReactDOM from 'react-dom'
import { DisabledNextButton } from './elements/disabled_next_btn'
import { EnabledNextButton } from './elements/enabled_next_btn'
import { AlertMessageBox } from './elements/alert_message_box'

export default class TeamName extends React.Component {

  getInputDomData() {
    return ReactDOM.findDOMNode(this.refs.team_name).value.trim()
  }

  handleSubmit(e) {
    e.preventDefault()
    this.props.postTeamName(this.getInputDomData())
  }

  render() {
    return (
      <div className="row">
          {/* <!-- START app/View/Signup/team.ctp --> */}
          <div className="panel panel-default panel-signup">
              <div className="panel-heading signup-title">What do you want to call your Goalous team?</div>
              <div className="signup-description">Goalous team name sample text Goalous team name sample text Goalous team name sample text Goalous team  name sample text Goalous team name sample text.
                  <br/> Goalous team name sample text Goalous team name sample text Goalous team name sample text.</div>

              <form className="form-horizontal" method="post" acceptCharset="utf-8"
                    onSubmit={(e) => this.handleSubmit(e) } >
                  <div className="panel-heading signup-itemtitle">Goalous team name</div>
                  <input className="form-control signup_input-design" ref="team_name" placeholder="例) チームGoalous" maxLength="128" type="text"
                         onChange={ () => this.props.inputTeamName(this.getInputDomData()) } />

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
          {/* <!-- END app/View/Signup/team.ctp --> */}
      </div>
    )
  }
}
