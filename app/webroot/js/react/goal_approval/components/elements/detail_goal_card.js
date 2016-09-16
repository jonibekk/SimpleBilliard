import React from 'react'
import { Link } from 'react-router'

export class GoalCard extends React.Component {
  render() {
    return (
      <div className="goals-approval-detail-goal mod-bgglay" className={`goals-approval-detail-goal ${!this.props.is_leader ? 'mod-bgglay' : '' }`}>
          <div className="goals-approval-detail-table">
              <img className="goals-approval-detail-image" src={ this.props.goal.small_img_url } alt="" width="32" height="32" />
              <div className="goals-approval-detail-info">
                      <p><i className="fa fa-folder-o" aria-hidden="true"></i> { this.props.goal.category.name }</p>
                      <p>{ this.props.goal.name }</p>
                  <div className="goals-approval-detail-tkr">
                      <h2 className="goals-approval-detail-tkrtitle"><i className="fa fa-key" aria-hidden="true"></i> Top key result</h2>
                      <ul className="goals-approval-detail-tkrlist">
                          <li>{ this.props.goal.key_result.name }</li>
                          <li>{ this.props.goal.key_result.value }</li>
                          <li>{ this.props.goal.key_result.desc }</li>
                      </ul>
                  </div>
                  <a href="" className="goals-approval-detail-tkrlink"><i className="fa fa-angle-down" aria-hidden="true"></i> <span className="goals-approval-interactive-link">View previous</span></a>
              </div>
          </div>
      </div>
    )
  }
}
