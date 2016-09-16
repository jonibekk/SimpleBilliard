import React from 'react'
import { Link } from 'react-router'

export class GoalCard extends React.Component {
  render() {
    return (
      <div className="goals-approval-detail-goal mod-bgglay">
          <div className="goals-approval-detail-table">
              <img className="goals-approval-detail-image" src={ this.props.goal.photo_file_name } alt="" width="32" height="32" />
              <div className="goals-approval-detail-info">
                      <p><i className="fa fa-folder-o" aria-hidden="true"></i> Duty</p>
                      <p>I can get new customers everyday.I can get new customers everyday.I can get new customers everyday.I can get new customers everyday.I can get new customers everyday.I can get new customers everyday.I can get new customers everyday.I can get new customers everyday.I can get new cusmers everyday.I can get new customers everyday.</p>
                  <div className="goals-approval-detail-tkr">
                      <h2 className="goals-approval-detail-tkrtitle"><i className="fa fa-key" aria-hidden="true"></i> Top key result</h2>
                      <ul className="goals-approval-detail-tkrlist">
                          <li>tKR name</li>
                          <li>tKR value</li>
                          <li>tKR Desc</li>
                      </ul>
                  </div>
                  <a href="" className="goals-approval-detail-tkrlink"><i className="fa fa-angle-down" aria-hidden="true"></i> <span className="goals-approval-interactive-link">View previous</span></a>
              </div>
          </div>
      </div>
    )
  }
}

GoalCard.propTypes = {
}
