import React from 'react'

export class UserCard extends React.Component {
  render() {
    const goal_member = this.props.goal_member

    if (Object.keys(goal_member).length == 0) {
      return null
    }

    return (
      <div className={`goals-approval-detail-user ${!goal_member.is_leader ? 'mod-bgglay' : '' }`}>
        <div className="goals-approval-detail-table">
          <img className="goals-approval-detail-image" src={ goal_member.user.small_img_url } alt="" width="32"
               height="32"/>
          <div className="goals-approval-detail-info">
            <p className="goals-approval-detail-info-name">{ goal_member.user.display_username }</p>
            <p>{ goal_member.type }</p>
            <div className={`${goal_member.is_leader ? 'none' : ''}`}>
              <p>({ __('leader') }:{ goal_member.goal.leader.user.display_username })</p>
              <p className="goals-approval-detail-info-name">Role:{ goal_member.role }</p>
              <p>{ goal_member.description }</p>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

UserCard.propTypes = {
  goal_member: React.PropTypes.object
}
UserCard.defaultProps = { goal_member: {} }
