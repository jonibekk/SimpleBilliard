import React from 'react'
import {Link} from 'react-router'
import {GoalMember} from '~/common/constants/Model'

export default class CoacheeCard extends React.Component {
  render() {
    const ApprovalStatus = GoalMember.ApprovalStatus
    const Type = GoalMember.Type
    const goal_member = this.props.goal_member
    const role = goal_member.type == Type.OWNER ? __('Leader') : __('Collaborator')
    const is_incomplete = goal_member.approval_status != ApprovalStatus.DONE && goal_member.approval_status != ApprovalStatus.WITHDRAWN
    const status = (() => {
      if (goal_member.is_target_evaluation) {
        return __('Evaluated')
      }
      if (goal_member.approval_status == ApprovalStatus.NEW || goal_member.approval_status == ApprovalStatus.REAPPLICATION) {
        return __('Waiting for approval')
      }
      if (goal_member.approval_status == ApprovalStatus.WITHDRAWN) {
        return __('Withdrawn')
      }
      return __('Not Evaluated')
    })()

    return (
      <li className={`goals-approval-list-item ${ is_incomplete ? "is-incomplete is-waiting" : "is-complete" }`}>
          <Link className="goals-approval-list-item-link" to={ `/goals/approval/detail/${goal_member.id}` }>
              <img className="goals-approval-list-item-image" src={ goal_member.user.small_img_url } alt="" width="32" height="32" />
              <div className="goals-approval-list-item-info">
                  <p className="goals-approval-list-item-info-user-name">{ goal_member.user.display_username }</p>
                  <p className="goals-approval-list-item-info-goal-name">{ goal_member.goal.name }</p>
                  <p className="goals-approval-list-item-info-goal-attr">{ role }・<span className="mod-status">{ status }</span></p>
              </div>
              <p className="goals-approval-list-item-detail">
                <i className="fa fa-check" aria-hidden="true"></i>
              </p>
          </Link>
      </li>
    )
  }
}

CoacheeCard.propTypes = {
  goal_member: React.PropTypes.object.isRequired
}
