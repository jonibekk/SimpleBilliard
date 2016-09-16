import React from 'react'
import { Link } from 'react-router'

export class UserCard extends React.Component {
  render() {
    return (
      <div className="goals-approval-detail-user">
          <div className="goals-approval-detail-table">
              <img className="goals-approval-detail-image" src={ this.props.collaborator.user.photo_file_name } alt="" width="32" height="32" />
              <div className="goals-approval-detail-info">
                  <p className="goals-approval-detail-info-name">{ this.props.collaborator.user.display_username }</p>
                  <p>{ this.props.collaborator.role }
                      ({ __('leader') }:{ this.props.leader.display_username })</p>
                  <p className="goals-approval-detail-info-name">Role:Sales</p>
                  <p>I can get new customers everyday.</p>
              </div>
          </div>
      </div>
    )
  }
}

UserCard.propTypes = {
  collaborator: React.PropTypes.object.required,
  leader: React.PropTypes.object.required
}
