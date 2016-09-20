import React from 'react'

export class UserCard extends React.Component {
  render() {
    if(Object.keys(this.props.collaborator).length == 0) {
      return null
    }

    return (
      <div className={`goals-approval-detail-user ${!this.props.collaborator.is_leader ? 'mod-bgglay' : '' }`}>
          <div className="goals-approval-detail-table">
              <img className="goals-approval-detail-image" src={ this.props.collaborator.user.small_img_url } alt="" width="32" height="32" />
              <div className="goals-approval-detail-info">
                  <p className="goals-approval-detail-info-name">{ this.props.collaborator.user.display_username }</p>
                  <p>{ this.props.collaborator.type }</p>
                  <div className={`${this.props.collaborator.is_leader ? 'none' : ''}`}>
                      <p>({ __('leader') }:{ this.props.collaborator.goal.leader.user.display_username })</p>
                      <p className="goals-approval-detail-info-name">Role:{ this.props.collaborator.role }</p>
                      <p>{ this.props.collaborator.description }</p>
                  </div>
              </div>
          </div>
      </div>
    )
  }
}

UserCard.propTypes = {
  collaborator: React.PropTypes.object.isRequired
}
