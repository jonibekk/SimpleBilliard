import React from 'react'
import { Link } from 'react-router'

export class GoalCard extends React.Component {
  render() {
    return (
      <div>
          <div className="goals-approval-detail-goal">
              <div className="goals-approval-detail-table">
                  <img className="goals-approval-detail-image" src="" alt="" width="32" height="32" />
                  <div className="goals-approval-detail-info">
                      <p>goal name</p>
                  </div>
              </div>
          </div>
          <div className="goals-approval-detail-tkr">
              <h2>Top key result</h2>
              <ul className="">
                  <li>tKR name</li>
                  <li>tKR value</li>
                  <li>tKR Desc</li>
              </ul>
              <a href=""><i className="fa fa-eye" aria-hidden="true"></i> <span className="goals-approval-interactive-link">View previous</span></a>
          </div>
      </div>
    )
  }
}

GoalCard.propTypes = {
}
