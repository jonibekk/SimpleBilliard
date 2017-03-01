import React from 'react'

export default class UserCard extends React.Component {
  render() {
    const goal_member = this.props.goal_member

    if (Object.keys(goal_member).length == 0) {
      return null
    }

    return (
      <div className={`goals-approval-detail-user ${!goal_member.is_leader ? 'mod-bgglay' : '' }`}>
        <div className="goals-approval-detail-table">
          <a href={`/users/view_goals/user_id:${goal_member.user.id}`} target={cake.is_mb_app ? "_self" : "_blank"}>
            <img className="goals-approval-detail-image" src={ goal_member.user.small_img_url } alt="" width="32" height="32"/>
          </a>
          <div className="goals-approval-detail-info">
            <p className="goals-approval-detail-info-name">
              <a href={`/users/view_goals/user_id:${goal_member.user.id}`} className="goals-approval-detail-info-name-link" target={cake.is_mb_app ? "_self" : "_blank"}>
                { goal_member.user.display_username }
              </a>
            </p>
            <p>{ goal_member.type }</p>
            <div className={`${goal_member.is_leader ? 'none' : ''}`}>
              <p>({ __('Leader') }: <a href={`/users/view_goals/user_id:${goal_member.goal.leader.user.id}`} className="goals-approval-detail-info-name-link" target={cake.is_mb_app ? "_self" : "_blank"}>
                  { goal_member.goal.leader.user.display_username })
                </a>
              </p>
              <p className="goals-approval-detail-info-name">{__("Role")}: { goal_member.goal_member.role }</p>
              <p>{ goal_member.goal_member.description }</p>
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
