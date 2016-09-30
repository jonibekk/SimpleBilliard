import React from 'react'

export class UserCard extends React.Component {
  render() {
    const collaborator = this.props.collaborator

    if(Object.keys(collaborator).length == 0) {
      return null
    }

    return (
      <div className={`goals-approval-detail-user ${!collaborator.is_leader ? 'mod-bgglay' : '' }`}>
          <div className="goals-approval-detail-table">
              <img className="goals-approval-detail-image" src={ collaborator.user.small_img_url } alt="" width="32" height="32" />
              <div className="goals-approval-detail-info">
                  <p className="goals-approval-detail-info-name">{ collaborator.user.display_username }</p>
                  <p>{ collaborator.type }</p>
                  <div className={`${collaborator.is_leader ? 'none' : ''}`}>
                      <p>({ __('leader') }:{ collaborator.goal.leader.user.display_username })</p>
                      <p className="goals-approval-detail-info-name">Role:{ collaborator.role }</p>
                      <p>{ collaborator.description }</p>
                  </div>
              </div>
          </div>
      </div>
    )
  }
}

UserCard.propTypes = {
  collaborator: React.PropTypes.object
}
UserCard.defaultProps = { collaborator: {} }
