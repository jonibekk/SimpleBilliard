import React from 'react'
import { Link } from 'react-router'

export class UserCard extends React.Component {
  render() {
    return (
      <div className={`goals-approval-detail-user ${!this.props.is_leader ? 'mod-bgglay' : '' }`}>
          <div className="goals-approval-detail-table">
              <img className="goals-approval-detail-image" src={ this.props.collaborator.user.small_img_url } alt="" width="32" height="32" />
              <div className="goals-approval-detail-info">
                  <p className="goals-approval-detail-info-name">{ this.props.collaborator.user.display_username }</p>
                  <p>{ this.props.collaborator.type }</p>
                  <div className={`${is_leader ? 'none' : ''}`}>
                      <p>({ __('leader') }:{ this.props.collaborator.goal.leader.user.display_username })</p>
                      <p className="goals-approval-detail-info-name">Role:{ this.props.collaborator.role }</p>
                      <p>I can get new customers everyday.</p>
                  </div>
              </div>
          </div>
      </div>
    )
  }
}
